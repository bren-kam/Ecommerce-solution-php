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
    private $_view_base;

    /**
     * The model path for the current controller
     * @var string
     */
    protected $model_path = '';

    /**
     * Setup standard reflection
     *
     * @param string $view_base [optional]
     */
    public function __construct( $view_base = '') {
        // Define the view base
        $this->_view_base = $view_base;

        // Autoload different classes
        spl_autoload_register( array( $this, '_load_exception' ) );

        // This always needs to be included
        lib('responses/response');
        spl_autoload_register( array( $this, '_load_response' ) );

        // Load models
        spl_autoload_register( array( $this, '_load_model' ) );

        $this->_available_actions = array();
        $reflectionClass = new ReflectionClass( get_class( $this ) );

        // All methods from any Controller inheriting from BaseController
        foreach ( $reflectionClass->getMethods() as $method ) {
            $methodName = $method->getName();
            $nonce = nonce::create( $methodName );
            $this->_available_actions[$nonce] = $methodName;
        }
    }

    /**
     * Cant be changed, every Controller will have a method for each possible action
     *
     * @throws ControllerException
     */
    public final function run() {
        // 2 because this method will count
        if ( sizeof( $this->_available_actions ) < 2 )
            throw new ControllerException( "No actions registered for controller " . get_class( $this ) );

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
     * @return TemplateResponse
     */
    protected function get_template_response( $file ) {
        return new TemplateResponse( $this->_view_base . $file );
    }

    /**
     * Load an exception
     *
     * @var string $exception
     */
    private function _load_exception( $exception ) {
        if ( !stristr( $exception, 'Exception' ) )
            return;

        // Form the model name, i.e., AccountListing to account-listing.php
        $exception_file = strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $exception ) ) . '.php';

        $full_path = LIB_PATH . 'exceptions/' . $exception_file;

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
        $model_file = strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $model ) ) . '.php';

        // Define the paths to search
    	$paths = array( MODEL_PATH, LIB_PATH . 'models/' );

        // Loop through each path and see if it exists
        foreach ( $paths as $path ) {
            $full_path = $path . $this->model_path . '/';

            if ( is_file( $full_path . $model_file ) ) {
                require_once $full_path;
                break;
            }
        }
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
}