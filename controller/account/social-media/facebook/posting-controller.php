<?php
class PostingController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'social-media/facebook/posting/';
        $this->section = 'social-media';
        $this->title = _('Posting') . ' | ' . _('Facebook') . ' | ' . _('Social Media');
    }

    /**
     * Redirect to Facebook
     *
     * @return TemplateResponse
     */
    protected function index() {
        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
            return new RedirectResponse('/social-media/facebook/');

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        return $this->get_template_response( 'index' )
            ->select( 'posting', 'view' )
            ->set( compact( 'page' ) );
    }

    /**
     * Redirect to Facebook
     *
     * @return TemplateResponse
     */
    protected function post() {
        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
            return new RedirectResponse('/social-media/facebook/');

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        // Get variables
        $posting = new SocialMediaPosting();
        $posting->get( $page->id );

        $fb = new Fb('posting');


        $timezone = $this->user->account->get_settings('timezone');

        // Make sure they set timezone
        if ( empty( $timezone ) ) {
            $this->notify( _('Please set your timezone and return to the posting page.'), false );
            return new RedirectResponse( '/social-media/facebook/settings/' );
        }

        // Figure out what the time is
        $now = new DateTime( dt::adjust_timezone( 'now', Config::setting('server-timezone'), $timezone ) );

        // Add validation
        $v = new Validator('fFBPost');
        $v->trigger = true;
        $v->add_validation( 'taPost', 'req', _('The Post field is required') );

        $errs = '';
        $js_validation = $v->js_validation();

        if ( 0 != $posting->fb_page_id ) {
            $fb->setAccessToken( $posting->access_token );

            try {
                $accounts = $fb->api( '/' . $posting->fb_user_id . '/accounts' );
            } catch( FacebookApiException $e ) {
                switch ( $e->getCode() ) {
                    case 60:
                        $this->notify( $e->getMessage(), false );
                        return new RedirectResponse('/social-media/facebook/posting/');
                    break;

                    case 190:
                        return new RedirectResponse( url::add_query_arg( array(
                            'client_id' => $fb->id
                            , 'redirect_uri' => url::add_query_arg( array(
                                'fb_page_id' => $posting->fb_page_id
                                , 'gsr_redirect' => 'http://account.' . DOMAIN . '/social-media/facebook/posting/post/'
                            ), 'http://apps.facebook.com/op-posting/' )
                        ), 'https://www.facebook.com/dialog/oauth' ) );
                    break;

                    default:
                        $errs = _('Due to a recent change to your Facebook account, your Online Specialist needs to reconnect your Posting App to Facebook.');
                    break;
                }
            }

            $pages = ar::assign_key( $accounts['data'], 'id' );

            if ( !isset( $pages[$posting->fb_page_id] ) ) {
                // Reset app
                $posting->fb_page_id = 0;
                $posting->fb_user_id = 0;
                $posting->access_token = '';
                $posting->save();

                // Let them know what happened
                $this->notify( _('Your app lost permission with Facebook and has been reset automatically. Please reconnect with Facebook') );

                return new RedirectResponse('/social-media/facebook/posting/post/');
                //echo "Contact Support!";
            }
        } elseif ( !$posting->key ) {
            $posting->sm_facebook_page_id = $page->id ;
            $posting->key = md5( $this->user->id . microtime() . $page->id );
            $posting->create();
        }

        if ( $this->verified() && isset( $pages ) ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                $date_posted = $_POST['tDate'];

                // Turn it into machine-readable time
                if ( !empty( $_POST['tTime'] ) ) {
                    list( $time, $am_pm ) = explode( ' ', $_POST['tTime'] );

                    if ( 'pm' == strtolower( $am_pm ) ) {
                        list( $hour, $minute ) = explode( ':', $time );

                        $date_posted .= ( 12 == $hour ) ? ' ' . $time . ':00' : ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
                    } else {
                        $date_posted .= ' ' . $time . ':00';
                    }
                }

                // Adjust for time zone
                $new_date_posted = new DateTime( dt::adjust_timezone( $date_posted, $timezone, Config::setting('server-timezone') ) );

                // Make sure we don't have anything extra
                $_POST['taPost'] = format::convert_characters( $_POST['taPost'] );

                // Get link
                preg_match( '/(?:(http|ftp|https):\/\/|www\.)[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?/', $_POST['taPost'], $matches );

                if ( !empty( $matches[0] ) ) {
                    $link = ( stristr( $matches[0], 'http://' ) ) ? $matches[0] : 'http://' . $matches[0];
                } else {
                    $link = '';
                }

                $post = new SocialMediaPostingPost();
                $post->sm_facebook_page_id = $page->id;
                $post->access_token = $pages[$posting->fb_page_id]['access_token'];
                $post->post = $_POST['taPost'];
                $post->link = $link;
                $post->date_posted = $new_date_posted->format('Y-m-d H:i:s');

                if ( time() >= $new_date_posted->getTimestamp() ) {
                    $fb->setAccessToken( $post->access_token );

                    // Information:
                    // http://developers.facebook.com/docs/reference/api/page/#posts
                    $fb->api( $posting->fb_page_id . '/feed', 'POST', array( 'message' => $post->post, 'link' => $post->link ) );
                } else {
                    $post->status = 0;
                }

                // Create post
                $post->create();

                // Let them it's been posted
                $this->notify( _('Your message has been successfully posted or scheduled to your Facebook page!') );
            }
        } else {
            $new_date_posted = $now;
            $post = new SocialMediaPostingPost();
        }

        $this->resources
            ->css(  )
            ->javascript(  );

        return $this->get_template_response( 'post' )
            ->add_title( _('Post') )
            ->select( 'posting' )
            ->set( compact( 'posting', 'errs', 'js_validation', 'page', 'pages', 'now', 'new_date_posted', 'post' ) );
    }

    /***** AJAX *****/

    /**
     * List
     *
     * @return DataTableResponse
     */
    protected function list_posts() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) ) {
            $dt->set_data( array() );
            return $dt;
        }

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id ) {
            $dt->set_data( array() );
            return $dt;
        }

        // Set variables
        $dt->order_by( '`post`', '`status`', '`date_posted`' );
        $dt->add_where( " AND `sm_facebook_page_id` = " . (int) $page->id );
        $dt->search( array( '`post`' => true ) );

        $post = new SocialMediaPostingPost();

        // Get autoresponder
        $posts = $post->list_all( $dt->get_variables() );
        $dt->set_row_count( $post->count_all( $dt->get_count_variables() ) );

        // Setup variables
        $confirm = _('Are you sure you want to cancel this post? This cannot be undone.');
        $delete_post_nonce = nonce::create( 'delete_post' );
        $timezone = $this->user->account->get_settings( 'timezone' );
        $server_timezone = Config::setting('server-timezone');
        $data = array();

        // Create output
        if ( is_array( $posts ) )
        foreach ( $posts as $post ) {
            // Set the actions
            $actions = '<br />
            <div class="actions">
                <a href="' . url::add_query_arg( array( 'smppid' => $post->id, '_nonce' => $delete_post_nonce ), '/social-media/facebook/posting/delete-post/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
            </div>';

            $content = $post->post;

            // Determine what to do based off the status
            switch ( $post->status ) {
                default:
                case -1:
                    $status = _('Error');

                    $content .= '<br /><br /><span class="error">' . $post->error . '</span>';
                break;

                case 0:
                    $status = _('Scheduled');
                break;

                case 1:
                    $actions = '';

                    $status = _('Posted');
                break;
            }

            $data[] = array(
                $content . $actions,
                $status,
                dt::adjust_timezone( $post->date_created, $server_timezone, $timezone, 'F jS, Y g:i a' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     *
     * @return AjaxResponse
     */
    public function delete_post() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['smppid'], $_SESSION['sm_facebook_page_id'] ), _('You cannot delete this post') );

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        $response->check( $page->id, _('You cannot delete this post') );

        if ( $response->has_error() )
            return $response;

        $post = new SocialMediaPostingPost();
        $post->get( $_GET['smppid'], $page->id );

        if ( 1 != $post->status )
            $post->remove();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


