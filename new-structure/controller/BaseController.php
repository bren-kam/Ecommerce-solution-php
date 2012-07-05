<?php
abstract class BaseController {

    private $availableActions;

    public function __construct() {
        $this->availableActions = array();
        $reflectionClass = new ReflectionClass( get_class( $this ) );
        foreach ( $reflectionClass->getMethods() as $method ) { //All methods from any Controller inheriting from BaseController
            $methodName = $method->getName();
            $nonce = nonce::create( $methodName );
            $this->availableActions[$nonce] = $methodName;
        }
    }

    //Cant be changed, every Controller will have a method for each possible action
    public final function run() {
        if ( sizeof( $this->availableActions ) < 2 ) {//2 because this method will count
            throw new ControllerException( "No actions registered for controller " . get_class( $this ) );
        }
        $actionName = $_REQUEST['nonce'];
        $methodName = $this->availableActions[$actionName];
        if ( NULL == $methodName ) {
            throw new ControllerException( "There is no such action" );
        }
        $this->$methodName();
    }


}

//Not sure if this will work, if not, we just need to apply it on each controller
$controller = new $this();
$controller->run();
