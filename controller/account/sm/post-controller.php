<?php

class PostController extends BaseController {

    public function __construct() {
        $this->title = 'Posting | SM';
        parent::__construct();
    }

    /**
     * Index
     * @return RedirectResponse TemplateResponse
     */
    public function index() {

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account_list = $website_sm_account->get_all( $this->user->account->id );

        // if ( empty( $website_sm_account_list ) ) {
        //     return new RedirectResponse( '/sm/' );
        // }

        $website_sm_accounts = [];
        foreach ( $website_sm_account_list as $a ) {
            $website_sm_accounts[$a->id] = $a;
        }

        $timezones = array_slice( data::timezones( false, false, true ), 4, 4 );
        $period = new DatePeriod(
             new DateTime('2015-01-01 00:00:00'),
             new DateInterval('PT15M'),
             new DateTime('2015-01-02 00:00:00')
        );
        $time_options = [];
        foreach ( $period as $time ) {
            $time_options[$time->format('H:i')] = $time->format('g:i A');
        }

        if ( $this->verified() ) {
            foreach ( $_POST['website_sm_accounts'] as $website_sm_account_id ) {
                $website_sm_post = new WebsiteSmPost();
                $website_sm_post->website_sm_account_id = $website_sm_account_id;
                $website_sm_post->content = $_POST['content'];
                $website_sm_post->photo = $_POST['photo'];
                $website_sm_post->link = $_POST['link'];
                if ( $website_sm_post->link && strpos( $website_sm_post->link, 'http' ) === false ) {
                    $website_sm_post->link = 'http://' . $website_sm_post->link;
                }
                $website_sm_post->timezone = $_POST['timezone'];

                $post_at = '';
                if ( $_POST['post-at']['day'] && $_POST['post-at']['time'] ) {
                    $post_at = "{$_POST['post-at']['day']} {$_POST['post-at']['time']}";
                    $post_at_datetime = new DateTime( $post_at );
                    $website_sm_post->post_at = $post_at_datetime->format('Y-m-d H:i:s');
                } else {
                    $website_sm_post->post_at = null;
                }

                $website_sm_post->create();

                $this->log( 'create-social-media-post', $this->user->contact_name . ' created a social media post on ' . $this->user->account->title, $website_sm_post->id );

                if ( $website_sm_post->can_post() ) {
                    $website_sm_account = $website_sm_accounts[$website_sm_account_id];

                    $website_sm_post->website_id = $this->user->account->id;
                    $website_sm_post->sm = $website_sm_account->sm;
                    $website_sm_post->post();
                }
            }

            return new RedirectResponse( '/sm/post/' );
        }


        $this->resources->css( 'sm/post', 'media-manager' )
            ->css_url( Config::resource('bootstrap-datepicker-css') )
            ->javascript( 'sm/post/index', 'media-manager' )
            ->javascript_url( Config::resource('bootstrap-datepicker-js'), Config::resource( 'jqueryui-js' ) );

        return $this->get_template_response( 'sm/post/index' )
            ->menu_item( 'sm/post' )
            ->kb(99)
            ->set( compact( 'website_sm_accounts', 'website_sm_posts', 'timezones', 'time_options' ) );

    }

    /**
     * List All
     * @return CustomResponse
     */
    public function list_all() {

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account_list = $website_sm_account->get_all( $this->user->account->id );
        $website_sm_accounts = [];
        foreach ( $website_sm_account_list as $a ) {
            $website_sm_accounts[$a->id] = $a;
        }

        $where = '';
        if ( isset($_REQUEST['posted']) ) {
            $where .= " AND posted = {$_REQUEST['posted']}";
        }

        if ( $_REQUEST['website_sm_account_id'] ) {
            $where .= " AND p.website_sm_account_id = {$_REQUEST['website_sm_account_id']}";
        }

        $website_sm_post = new WebsiteSmPost();
        $website_sm_posts = $website_sm_post->list_all([
            " AND a.website_id = {$this->user->account->id} {$where}"
            , ""
            , " ORDER BY p.post_at ASC "
            , 999999
        ]);

        $timezones = array_slice( data::timezones( false, false, true ), 4, 4 );

        $response = new CustomResponse( $this->resources, '/sm/post/list' );
        $response->set( compact( 'website_sm_accounts', 'website_sm_posts', 'timezones' ) );
        return $response;

    }

    /**
     * Get
     * @return AjaxResponse
     */
    public function get() {
        $response = new AjaxResponse( true );

        $post = new WebsiteSmPost();
        $post->get( $_GET['id'], $this->user->account->id );

        $response->add_response( 'post', $post );
        return $response;
    }

    /**
     * Edit
     * @return AjaxResponse
     */
    public function edit() {
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $post = new WebsiteSmPost();
        $post->get( $_POST['id'], $this->user->account->id );

        if ( !$post->id )
            return $response;

        $post_at = '';
        if ( $_POST['post-at']['day'] && $_POST['post-at']['time'] ) {
            $post_at = "{$_POST['post-at']['day']} {$_POST['post-at']['time']}";
            $post_at_datetime = new DateTime( $post_at );
            $post->post_at = $post_at_datetime->format('Y-m-d H:i:s');
            $post->save();

            $response->notify("Post #{$post->id} will run on " . $post_at_datetime->format('l jS F, h:i:s A'));
            $this->log( 'update-social-media-post', $this->user->contact_name . ' updated a social media post on ' . $this->user->account->title, $post->id );

            $response->add_response('post_at', $post_at_datetime->format('l jS F, h:i:s A'));
        }

        return $response;
    }


    /**
     * Remove
     * @return AjaxResponse
     */
    public function remove() {
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $post = new WebsiteSmPost();
        $post->get( $_REQUEST['id'], $this->user->account->id );

        if ( !$post->id )
            return $response;

        $post->remove();

        $response->notify('Post removed!');
        $this->log( 'remove-social-media-post', $this->user->contact_name . ' removed a social media post on ' . $this->user->account->title, $post->id );

        return $response;
    }

}