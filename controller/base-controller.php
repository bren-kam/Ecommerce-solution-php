<?php

/**
 * The base class for all other controllers
 */
abstract class BaseController {
    /**
     * Hold the method that was called
     * @var string
     */
    protected $method;

    /**
     * Define the base for the views
     * @var string
     */
    protected $view_base;

    /**
     * Define the section name for views
     * @var string
     */
    protected $section;

    /**
     * Define the title of the section
     * @var string
     */
    protected $title;

    /**
     * The model path for the current controller
     * @var string
     */
    protected $model_path = '';

    /**
     * Hold the user
     * @var User
     */
    protected $user;

    /**
     * Hold the resources
     * @var Resources
     */
    protected $resources;

    /**
     * Setup standard reflection
     */
    public function __construct() {
        $this->_available_actions = array();

        // Any initialization things, such as checking user login
        $this->_init();
    }

    /**
     * Initialize Controller
     */
    private function _init() {
        // This always needs to be included
        lib('responses/response');
        spl_autoload_register( array( $this, '_load_response' ) );

        // Autoload different classes
        spl_autoload_register( array( $this, '_load_helper' ) );

        // Load models
        spl_autoload_register( array( $this, '_load_model' ) );

        // Make sure the user is logged in
        if ( ( 'admin' == SUBDOMAIN || 'account' == SUBDOMAIN ) && !$this->get_logged_in_user() )
            $this->login();

        // Set the resources up
        $this->resources = new Resources();
    }

    /**
     * Cant be changed, every Controller will have a method for each possible action
     *
     * @throws ControllerException
     */
    public final function run( $method ) {
        /**
         * @var Response $response
         */
        $this->method = $method;
        $response = $this->$method();
        $response->send_response();
    }

    /**
     * Create a notification
     *
     * @param string $message
     * @param bool $success
     */
    protected function notify( $message, $success = true ) {
        $notification = new Notification();
        $notification->user_id = $this->user->user_id;
        $notification->message = $message;
        $notification->success = $success;
        $notification->create();
    }

    /**
     * Determine if this page has been verified
     *
     * @return bool
     */
    protected function verified() {
        return ( isset( $_REQUEST['_nonce'] ) ) ? nonce::verify( $_REQUEST['_nonce'], $this->method ) : false;
    }
    
    /**
     * Return a new template response with the right path
     *
     * @param string $file
     * @param string $title [optional]
     * @return TemplateResponse
     */
    protected function get_template_response( $file, $title = '' ) {
        // Determine title
        $title = ( empty( $title ) ) ? $this->section : $title . ' | ' . $this->section;

        // Setup new template response
        $template_response = new TemplateResponse( $this->resources, $this->view_base . $file, $title );
        $template_response->set( 'user', $this->user );
        $template_response->set( 'section', $this->section );
        $template_response->set( $this->section, true );

        if ( is_null( $this->title ) )
            $this->title = ucwords( $this->section );

        $template_response->set( 'title', $this->title );
        $template_response->set( 'view_base', $this->view_base );

        return $template_response;
    }

    /**
     * Get the logged in user if we can
     *
     * @return bool
     */
    protected function get_logged_in_user() {
        if ( !$encrypted_email = get_cookie( AUTH_COOKIE ) )
			return false;

        // Get the email
        $email = security::decrypt( base64_decode( $encrypted_email ), security::hash( COOKIE_KEY, 'secure-auth' ) );

        // Create new user
        $this->user = new User( 'admin' == SUBDOMAIN );
        $this->user->get_by_email( $email );

        // Check what permission needs to be checked
        $permission = ( 'admin' == SUBDOMAIN ) ? 6 : 1;

        // See if we can get the user
        if ( !$this->user->has_permission( $permission ) )
            return false;

        // Account Side
        if ( 'admin' != SUBDOMAIN ) {
            // We need to get the account(s)
            $account_id = get_cookie('wid');
            $account = new Account();

            // Grab all the accounts for the type of user they are
            if ( in_array( $this->user->role, array( User::ROLE_AUTHORIZED_USER, User::ROLE_MARKETING_SPECIALIST ) ) ) {
                $this->user->accounts = $account->get_by_authorized_user( $this->user->id );
            } else {
                $this->user->accounts = $account->get_by_user( $this->user->id );
            }

            // If they have a specific account, get that
            if ( $account_id ) {
                $account->get( $account_id );

                if ( $account->id )
                    $this->user->account = $account;

                /**
                 * @var Account $account
                 */
                if ( !$this->user->account && is_array( $this->user->accounts ) ) {
                    foreach ( $this->user->accounts as $account ) {
                        if ( $account_id == $account->id ) {
                            $this->user->account = $account;
                            break;
                        }
                    }

                    if ( !$this->user->account )
                        $this->user->account = reset( $this->user->accounts );

                    // Set the cookie
                    if ( $this->user->account )
                        set_cookie( 'wid', $this->user->account->id, 172800 );
                }
            }

            if ( !$this->user->account && '/home/select-account/' != $_SERVER['REQUEST_URI'] ) {
                $url = ( count( $this->user->accounts ) > 0 ) ? '/home/select-account/' : '/logout/';
                url::redirect($url);
                return true;
            }
        }

        return true;
    }

    /**
     * Force user to login
     */
    protected function login() {
        // Remove any cookie that might exist
        remove_cookie( AUTH_COOKIE );

        // Check if we have a referer
        $referer = ( isset( $_SERVER['REDIRECT_URL'] ) ) ? $_SERVER['REDIRECT_URL'] : '';

        if ( !empty( $_SERVER['QUERY_STRING'] ) )
            $referer .= '?' . $_SERVER['QUERY_STRING'];

        url::redirect( '/login/?r=' . urlencode( $referer ) );
    }

    /**
     * Store Session
     *
     * @return AjaxResponse
     */
    protected function store_session() {
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_POST['keys'] ) || !isset( $_POST['value'] ) )
            return $response;

        // Create array
        $session = &$_SESSION;

        foreach ( $_POST['keys'] as $key ) {
            if ( !isset( $session[$key] ) )
                $session[$key] = array();

            $session = &$session[$key];
        }

        $session = $_POST['value'];

        return $response;
    }

    /**
     * Load a response
     *
     * @var string $response
     */
    private function _load_response( $response ) {
        if ( !stristr( $response, 'Response' ) )
            return;

        // Form the model name, i.e., AccountListing to account-listing.php
        $response_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $response ) ) . '.php', 1 );

        $full_path = LIB_PATH . 'responses/' . $response_file;

        if ( is_file( $full_path ) )
            require_once $full_path;
    }

    /**
     * Load helpers
     *
     * @var string $helper
     */
    private function _load_helper( $helper ) {
        // Form the model name, i.e., AccountListing to account-listing.php
        $helper_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $helper ) ) . '.php', 1 );

        $full_path = LIB_PATH . 'helpers/' . $helper_file;

        if ( is_file( $full_path ) )
            require_once $full_path;
    }

    /**
     * Load a model
     *
     * @var string $model
     */
    private function _load_model( $model ) {
        // Form the model name, i.e., AccountListing to account-listing.php
        $model_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $model ) ) . '.php', 1 );

        // Define the paths to search
    	$paths = array( MODEL_PATH, LIB_PATH . 'models/' );

        // Loop through each path and see if it exists
        foreach ( $paths as $path ) {
            $full_path = $path . $this->model_path . $model_file;

            if ( is_file( $full_path ) ) {
                require_once $full_path;
                break;
            }
        }
    }
}