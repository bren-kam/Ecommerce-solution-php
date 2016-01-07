<?php
class SmController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'sm/';
        $this->title = 'Social Media';
    }

    /**
     * Redirect to Post
     *
     * @return RedirectResponse
     */
    protected function index() {
        return new RedirectResponse('/sm/post/');
        return $this->get_template_response( 'index' )
            ->menu_item( 'sm/account' );
    }

    /**
     * List All
     * @return DataTableResponse
     */
    public function list_all() {
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`sm`', '`sm_reference_id`', '`title`' );
        $dt->search( array( '`sm`' => false, '`sm_reference_id`' => false, '`title`' => false ) );
        $dt->add_where( " AND `website_id` = " . (int) $this->user->account->id );

        // Get SM Accounts
        $website_sm_account = new WebsiteSmAccount();
        $website_sm_accounts = $website_sm_account->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_sm_account->count_all( $dt->get_count_variables() ) );
        $delete_nonce = nonce::create( 'delete' );

        $data = [];
        foreach ( $website_sm_accounts as $website_sm_account ) {

            $actions = '<a href="/sm/delete/?id=' . $website_sm_account->id . '&_nonce=' . $delete_nonce . '" ajax="1" confirm="Do you want to remove this Social Media Account? Cannot be undone">Delete</a>';
            if ( $website_sm_account->sm == 'facebook' ) {
                $actions .= ' | <a href="/sm/settings/?id=' . $website_sm_account->id . '">Settings</a>';
            }

            $data[] = [
                $website_sm_account->title . '<br>' . $actions
                , $website_sm_account->sm
                , $website_sm_account->created_at
            ];
        }

        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     * @return RedirectResponse
     */
    public function delete() {

        $response = new RedirectResponse('/sm/');
        if ( !$this->verified() ) {
            return $response;
        }

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get( $_GET['id'], $this->user->account->id );
        if ( $website_sm_account->id ) {
            $website_sm_account->remove();
            $this->notify( 'Account Removed' );
            $this->log( 'delete-social-media-account', $this->user->contact_name . ' deleted a social media account on ' . $this->user->account->title, $_GET['id'] );
        }

        return $response;
    }

    /**
     * Settings
     * @return TemplateResponse || RedirectResponse
     */
    public function settings() {
        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get( $_REQUEST['id'], $this->user->account->id );

        if ( !$website_sm_account->id )
            return new RedirectResponse('/sm/');

        switch ( $website_sm_account->sm ) {
            case 'facebook':
                library('facebook_v4/facebook');
                Facebook\FacebookSession::setDefaultApplication( Config::key( 'facebook-key' ) , Config::key( 'facebook-secret' ) );
                $session = new Facebook\FacebookSession( $website_sm_account->auth_information_array['access-token'] );

                $request = new Facebook\FacebookRequest(
                    $session
                    , 'GET'
                    , '/me/accounts'
                );

                $response = $request->execute();
                $graphObject = $response->getGraphObject();
                $fb_pages_graph = $graphObject->getPropertyAsArray('data');
                $fb_pages = [];
                foreach ( $fb_pages_graph as $fb_page_graph ) {
                    $fb_page = $fb_page_graph->asArray();
                    $fb_pages[$fb_page['id']] = $fb_page;
                }
                break;
            default:
                return new RedirectResponse('/sm/');
                break;
        }

        if ( $this->verified() ) {

            switch ( $website_sm_account->sm ) {
                case 'facebook':
                    $post_as_page = isset( $fb_pages[ $_POST['fb_post_as'] ] );
                    $post_as = $post_as_page ? $fb_pages[ $_POST['fb_post_as'] ] : $website_sm_account->auth_information_array['me'];
                    $website_sm_account->title = $post_as['name'];
                    $website_sm_account->sm_reference_id = $post_as['id'];
                    $website_sm_account->auth_information_array['post-as'] = $post_as_page ? $post_as : 'me';
                    $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
                    $website_sm_account->save();
                    break;
            }

            $this->notify( 'Settings saved!' );
            $this->log( 'update-social-media-settings', $this->user->contact_name . ' updated social media settings on ' . $this->user->account->title );
            return new RedirectResponse( '/sm/' );
        }
        return $this->get_template_response('settings')
            ->menu_item('sm/settings')
            ->set( compact( 'website_sm_account', 'fb_pages', 'fs_venue_id' ) );
    }

    /**
     * Facebook Connect
     */
    public function facebook_connect() {

        library('facebook_v4/facebook');
        Facebook\FacebookSession::setDefaultApplication( Config::key( 'facebook-key' ) , Config::key( 'facebook-secret' ) );
        $helper = new Facebook\FacebookRedirectLoginHelper( Config::key( 'facebook-redirect' ) );
        $this->log( 'request-facebook-connection', $this->user->contact_name . ' tried to connect to facebook on ' . $this->user->account->title );

        url::redirect( $helper->getLoginUrl( ['publish_actions', 'manage_pages', 'publish_pages'] ) );
    }

    /**
     * Facebook Callback
     * @return RedirectResponse
     */
    public function facebook_callback() {
        try {
            library('facebook_v4/facebook');

            Facebook\FacebookSession::setDefaultApplication( Config::key( 'facebook-key' ) , Config::key( 'facebook-secret' ) );
            $helper = new Facebook\FacebookRedirectLoginHelper( Config::key( 'facebook-redirect' ) );

            $session = $helper->getSessionFromRedirect();

            if ( !$session ) {
                throw new Exception( 'Could not create Facebook Session, please accept application permissions in order to connect.' );
            }

            $request = new Facebook\FacebookRequest( $session, 'GET', '/me' );
            $response = $request->execute();

            $token = $session->getToken();
            $me = $response->getGraphObject()->asArray();

            $website_sm_account = new WebsiteSmAccount();
//            $website_sm_account->get_by_sm_reference_id( 'facebook', $me['id'], $_SESSION['sm-callback-website-id'] );
//            if ( !$website_sm_account->id ) {
                $website_sm_account->website_id = $_SESSION['sm-callback-website-id'];
                $website_sm_account->sm = 'facebook';
                $website_sm_account->sm_reference_id = $me['id'];
                $website_sm_account->title = $me['name'];
                $website_sm_account->photo = '';
                $website_sm_account->create();
                $this->notify("Connected {$website_sm_account->sm} account {$website_sm_account->title}");
                $this->log( 'facebook-connected', $this->user->contact_name . ' connected to Facebook on ' . $this->user->account->title );
//            } else {
//                $this->notify("Reconnecting {$website_sm_account->sm} existing account {$website_sm_account->title}");
//                $this->log( 'facebook-reconnected', $this->user->contact_name . ' reconnected to Facebook on ' . $this->user->account->title );
//            }
            $website_sm_account->auth_information_array = [
                'access-token' => $token
                , 'me' => $me
            ];
            $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
            $website_sm_account->save();

            url::redirect( 'http://' . url::domain( $_SESSION['sm-callback-referer'] ) . '/sm/settings/?id=' . $website_sm_account->id );
        } catch(FacebookRequestException $ex) {
            $this->notify( 'There was an error connecting with Facebook: ' . $ex->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
        } catch(\Exception $ex) {
            $this->notify( 'There was an error connecting with Facebook: ' . $ex->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
        }
    }

    /**
     * Twitter Connect
     */
    public function twitter_connect() {
        library('twitteroauth/autoload');
        $connection = new Abraham\TwitterOAuth\TwitterOAuth( Config::key( 'twitter-key' ) , Config::key( 'twitter-secret' ) );
        $request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => Config::key('twitter-redirect')] );
        $_SESSION['twitter-request-token'] = $request_token['oauth_token'];
        $_SESSION['twitter-request-token-secret'] = $request_token['oauth_token_secret'];

        url::redirect( $connection->url(
            'oauth/authorize'
            , ['oauth_token' => $request_token['oauth_token']]
        ) );

        $this->log( 'request-twitter-connection', $this->user->contact_name . ' tried to connect to Twitter on ' . $this->user->account->title );
    }

    /**
     * Twitter Callback
     * @return RedirectResponse
     */
    public function twitter_callback() {
        try {
            library('twitteroauth/autoload');

            $connection = new Abraham\TwitterOAuth\TwitterOAuth(
                Config::key( 'twitter-key' )
                , Config::key( 'twitter-secret' )
                , $_SESSION['twitter-request-token']
                , $_SESSION['twitter-request-token-secret']
            );

            $token = $connection->oauth( 'oauth/access_token', ["oauth_verifier" => $_REQUEST['oauth_verifier']] );

            $connection = new Abraham\TwitterOAuth\TwitterOAuth(
                Config::key( 'twitter-key' )
                , Config::key( 'twitter-secret' )
                , $token['oauth_token']
                , $token['oauth_token_secret']
            );
            $me = $connection->get( 'account/verify_credentials' );

            if ( !$me->id ) {
                throw new Exception('Could not get User Information');
            }

            $website_sm_account = new WebsiteSmAccount();
            $website_sm_account->get_by_sm_reference_id( 'twitter', $me->id, $_SESSION['sm-callback-website-id'] );
            if ( !$website_sm_account->id ) {
                $website_sm_account->website_id = $_SESSION['sm-callback-website-id'];
                $website_sm_account->sm = 'twitter';
                $website_sm_account->sm_reference_id = $me->id;
                $website_sm_account->title = $me->name;
                $website_sm_account->photo = '';
                $website_sm_account->create();
                $this->notify("Connected {$website_sm_account->sm} account {$website_sm_account->title}");
                $this->log( 'twitter-connected', $this->user->contact_name . ' connected to Twitter on ' . $this->user->account->title );
            } else {
                $this->notify("Reconnecting {$website_sm_account->sm} existing account {$website_sm_account->title}");
                $this->log( 'twitter-reconnected', $this->user->contact_name . ' reconnected to Facebook on ' . $this->user->account->title );
            }
            $website_sm_account->auth_information_array = [
                'access-token' => $token['oauth_token']
                , 'access-token-secret' => $token['oauth_token_secret']
            ];
            $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
            $website_sm_account->save();
            url::redirect( $_SESSION['sm-callback-referer'] );
        } catch (Exception $e) {
            $this->notify( 'There was an error connecting with Twitter: ' . $e->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
        }
    }


    /**
     * Get Logged In User
     * @return bool
     */
    protected function get_logged_in_user() {
        // connect_* are public, but need a referer and a website-id
        $connect_url = strpos( $_SERVER['REQUEST_URI'], '/sm/facebook-connect/' ) !== FALSE
                    || strpos( $_SERVER['REQUEST_URI'], '/sm/twitter-connect/' ) !== FALSE;

        if ( $connect_url ) {

            if ( !$_REQUEST['website-id'] || !$_SERVER['HTTP_REFERER'] ) {
                return false;
            }

            $_SESSION['sm-callback-website-id'] = $_REQUEST['website-id'];
            $_SESSION['sm-callback-referer'] = $_SERVER['HTTP_REFERER'];
            $_SESSION['sm-callback-user-id'] = $_REQUEST['user-id'];
            // for notifications
            $this->user = new stdClass;
            $this->user->user_id = $this->user->id = $_REQUEST['user-id'];

            return true;
        }

        $callback_url = strpos( $_SERVER['REQUEST_URI'], '/sm/facebook-callback/' ) !== FALSE
            || strpos( $_SERVER['REQUEST_URI'], '/sm/twitter-callback/' ) !== FALSE;

        if ( $callback_url ) {

            if ( !$_SESSION['sm-callback-website-id'] || !$_SESSION['sm-callback-referer'] || !$_SESSION['sm-callback-user-id'] ) {
                return false;
            }
            // for notifications
            $this->user = new stdClass;
            $this->user->user_id = $this->user->id = $_SESSION['sm-callback-user-id'];

            return true;
        }

        return parent::get_logged_in_user();

    }


}
