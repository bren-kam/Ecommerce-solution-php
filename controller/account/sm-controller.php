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
            $data[] = [
                $website_sm_account->title .
                ' <br> <a href="/sm/delete/?id=' . $website_sm_account->id . '&_nonce=' . $delete_nonce . '" ajax="1" confirm="Do you want to remove this Social Media Account? Cannot be undone">Delete</a>'
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
     * Facebook Connect
     */
    public function facebook_connect() {

        library('facebook_v4/facebook');
        Facebook\FacebookSession::setDefaultApplication( Config::key( 'facebook-key' ) , Config::key( 'facebook-secret' ) );
        $helper = new Facebook\FacebookRedirectLoginHelper( Config::key( 'facebook-redirect' ) );

        url::redirect( $helper->getLoginUrl( ['publish_actions'] ) );
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
        ];
        $website_sm_account->auth_information = json_encode( $website_sm_account->auth_information_array );
        $website_sm_account->save();
        url::redirect( $_SESSION['sm-callback-referer'] );
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
            $this->notify( 'There was an error connecting with Twitter: ' . $ex->getMessage(), false );
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
     * Get Logged In User
     * @return bool
     */
    protected function get_logged_in_user() {

        // connect_* are public, but need a referer and a website-id
        $public_url = strpos( $_SERVER['REQUEST_URI'], '/sm/facebook-connect/' ) !== FALSE
                   || strpos( $_SERVER['REQUEST_URI'], '/sm/twitter-connect/' ) !== FALSE;

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
