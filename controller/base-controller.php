<?php

/**
 * The base class for all other controllers
 */
abstract class BaseController {
    /**
     * Contain the available methods for subclass
     * @var array
     */
    private $_available_actions;

    /**
     * Define the base for the views
     * @param string
     */
    protected $view_base;

    /**
     * Define the section name for views
     * @param string
     */
    protected $section;

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
        $reflectionClass = new ReflectionClass( get_class( $this ) );

        // All methods from any Controller inheriting from BaseController
        foreach ( $reflectionClass->getMethods() as $method ) {
            $methodName = $method->getName();
            $nonce = nonce::create( $methodName );
            $this->_available_actions[$nonce] = $methodName;
        }

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
    public final function run() {
        $actionName = $_REQUEST['_nonce'];
        $methodName = $this->_available_actions[$actionName];

        if ( is_null( $methodName ) )
            throw new ControllerException( "There is no such action" );

        /**
         * @var Response
         */
        $response = $this->$methodName();
        $response->send_response();
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
        $template_response->add( 'user', $this->user );

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
        $this->user = new User();
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