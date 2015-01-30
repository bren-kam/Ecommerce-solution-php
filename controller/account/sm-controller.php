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
     * Redirect to Facebook
     *
     * @return RedirectResponse
     */
    protected function index() {
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
            if ( $website_sm_account->sm == 'facebook' || $website_sm_account->sm == 'foursquare' ) {
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
     * @return AjaxResponse
     */
    public function delete() {
        $response = new AjaxResponse( $this->verified() );
        if ( $response->has_error() ) {
            return $response;
        }

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get( $_GET['id'], $this->user->account->id );
        if ( $website_sm_account->id ) {
            $website_sm_account->remove();
            $response->notify( 'Account Removed' );
            $response->add_response( 'reload_datatable', 'reload_datatable' );
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
            case 'foursquare':
                $fs_venue_id = isset( $website_sm_account->auth_information_array['venue-id'] ) ? $website_sm_account->auth_information_array['venue-id'] : false;
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
                case 'foursquare':
                    $website_sm_account->auth_information_array['venue-id'] = $_POST['fs_venue_id'];
                    $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
                    $website_sm_account->save();
                    break;
            }


            $this->notify( 'Settings saved!' );
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

        url::redirect( $helper->getLoginUrl( ['publish_actions', 'manage_pages'] ) );
    }

    /**
     * Facebook Callback
     * @return RedirectResponse
     */
    public function facebook_callback() {
        library('facebook_v4/facebook');

        try {
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
        } catch(FacebookRequestException $ex) {
            $this->notify( 'There was an error connecting with Facebook: ' . $ex->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
        } catch(\Exception $ex) {
            $this->notify( 'There was an error connecting with Facebook: ' . $ex->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
        }

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get_by_sm_reference_id( 'facebook', $me['id'], $_SESSION['sm-callback-website-id'] );
        if ( !$website_sm_account->id ) {
            $website_sm_account->website_id = $_SESSION['sm-callback-website-id'];
            $website_sm_account->sm = 'facebook';
            $website_sm_account->sm_reference_id = $me['id'];
            $website_sm_account->title = $me['name'];
            $website_sm_account->photo = '';
            $website_sm_account->create();
        }
        $website_sm_account->auth_information_array = [
            'access-token' => $token
            , 'me' => $me
        ];
        $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
        $website_sm_account->save();

        url::redirect( 'http://' . url::domain( $_SESSION['sm-callback-referer'] ) . '/sm/settings/?id=' . $website_sm_account->id );
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
    }

    /**
     * Twitter Callback
     * @return RedirectResponse
     */
    public function twitter_callback() {
        library('twitteroauth/autoload');


        try {
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
        } catch (Exception $e) {
            $this->notify( 'There was an error connecting with Twitter: ' . $e->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
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
        }
        $website_sm_account->auth_information_array = [
            'access-token' => $token['oauth_token']
            , 'access-token-secret' => $token['oauth_token_secret']
        ];
        $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
        $website_sm_account->save();
        url::redirect( $_SESSION['sm-callback-referer'] );
    }

    /**
     * Foursquare Connect
     */
    public function foursquare_connect() {
        library('foursquare');

        $foursquare = new FoursquareAPI( Config::key('foursquare-client-id') , Config::key('foursquare-secret') );
        $auth_url = $foursquare->AuthenticationLink( Config::key('foursquare-redirect') );

        url::redirect( $auth_url );
    }

    /**
     * Foursquare Callback
     * @return RedirectResponse
     */
    public function foursquare_callback() {
        library('foursquare');

        $foursquare = new FoursquareAPI( Config::key('foursquare-client-id') , Config::key('foursquare-secret') );

        try {
            $token = $foursquare->GetToken( $_GET['code'], Config::key('foursquare-redirect') );
            $me_str = $foursquare->GetPrivate( 'users/self' );
            $me = json_decode( $me_str )->response->user;

            if ( !$me->id ) {
                throw new Exception('Could not get User Information');
            }
        } catch (Exception $e) {
            throw $e;
            $this->notify( 'There was an error connecting with Foursquare: ' . $e->getMessage(), false );
            url::redirect( $_SESSION['sm-callback-referer'] );
        }

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get_by_sm_reference_id( 'foursquare', $me->id, $_SESSION['sm-callback-website-id'] );
        if ( !$website_sm_account->id ) {
            $website_sm_account->website_id = $_SESSION['sm-callback-website-id'];
            $website_sm_account->sm = 'foursquare';
            $website_sm_account->sm_reference_id = $me->id;
            $website_sm_account->title = $me->firstName . ' ' . $me->lastName;
            $website_sm_account->photo = '';
            $website_sm_account->create();
        }

        $website_sm_account->auth_information_array = [
            'access-token' => $token
            , 'me' => $me
        ];
        $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
        $website_sm_account->save();

        $redirect_url = 'http://' . url::domain( $_SESSION['sm-callback-referer'] ) . '/sm/settings/?id=' . $website_sm_account->id;
        url::redirect( $redirect_url );
    }

    /**
     * Get Logged In User
     * @return bool
     */
    protected function get_logged_in_user() {

        // connect_* are public, but need a referer and a website-id
        $public_url = strpos( $_SERVER['REQUEST_URI'], '/sm/facebook-connect/' ) !== FALSE
                   || strpos( $_SERVER['REQUEST_URI'], '/sm/twitter-connect/' ) !== FALSE
                   || strpos( $_SERVER['REQUEST_URI'], '/sm/foursquare-connect/' ) !== FALSE;

        if ( $public_url ) {

            if ( !$_REQUEST['website-id'] || !$_SERVER['HTTP_REFERER'] ) {
                return false;
            }

            $_SESSION['sm-callback-website-id'] = $_REQUEST['website-id'];
            $_SESSION['sm-callback-referer'] = $_SERVER['HTTP_REFERER'];

            return true;
        }

        return parent::get_logged_in_user();

    }


}
