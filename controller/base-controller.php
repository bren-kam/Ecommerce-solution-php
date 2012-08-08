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
        if ( !$this->get_logged_in_user() )
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
     */
    public function notify( $message ) {
        $notification = new Notification();
        $notification->user_id = $this->user->user_id;
        $notification->message = $message;
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
        $this->user = new User( defined('ADMIN') );
        $this->user->get_by_email( $email );

        // Check what permission needs to be checked
        $permission = ( defined('ADMIN') ) ? 6 : 1;

        // See if we can get the user
        if ( !$this->user->has_permission( $permission ) )
            return false;

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