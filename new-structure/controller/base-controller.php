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
     * Setup standard reflection
     *
     * @param string $view_base [optional]
     */
    public function __construct( $view_base = '') {
        $this->_view_base = $view_base;

        $this->_available_actions = array();
        $reflectionClass = new ReflectionClass( get_class( $this ) );
        foreach ( $reflectionClass->getMethods() as $method ) { //All methods from any Controller inheriting from BaseController
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
}

//Not sure if this will work, if not, we just need to apply it on each controller
$controller = new $this();
$controller->run();
