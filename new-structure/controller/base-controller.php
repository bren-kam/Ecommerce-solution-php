<?php

require_once 'new-structure/lib/response.php';

/**
 * The base class for all other controllers
 */
abstract class BaseController {

    /**
     * Contain the available methods for subclass
     * @var array
     */
    private $available_actions;

    /**
     * Setup standard reflection
     */
    public function __construct() {
        $this->available_actions = array();
        $reflectionClass = new ReflectionClass( get_class( $this ) );
        foreach ( $reflectionClass->getMethods() as $method ) { //All methods from any Controller inheriting from BaseController
            $methodName = $method->getName();
            $nonce = nonce::create( $methodName );
            $this->available_actions[$nonce] = $methodName;
        }
    }

    /**
     * Cant be changed, every Controller will have a method for each possible action
     *
     * @throws ControllerException
     */
    public final function run() {
        // 2 because this method will count
        if ( sizeof( $this->available_actions ) < 2 )
            throw new ControllerException( "No actions registered for controller " . get_class( $this ) );

        $actionName = $_REQUEST['_nonce'];
        $methodName = $this->available_actions[$actionName];

        if ( is_null( $methodName ) )
            throw new ControllerException( "There is no such action" );

        /**
         * @var Response
         */
        $response = $this->$methodName();
        $response->send_response();
    }
}

//Not sure if this will work, if not, we just need to apply it on each controller
$controller = new $this();
$controller->run();
