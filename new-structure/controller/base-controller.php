<?php
abstract class BaseController {

    private $available_actions;

    public function __construct() {
        $this->available_actions = array();
        $reflection_class = new ReflectionClass( get_class( $this ) );
        foreach ( $reflection_class->getMethods() as $method ) { //All methods from any Controller inheriting from BaseController
            $method_name = $method->getName();
            $nonce = nonce::create( $method_name );
            $this->available_actions[$nonce] = $method_name;
        }
    }

    //Cant be changed, every Controller will have a method for each possible action
    public final function run() {
        if ( sizeof( $this->available_actions ) < 2 ) {//2 because this method will count
            throw new ControllerException( "No actions registered for controller " . get_class( $this ) );
        }
        $actionName = $_REQUEST['_nonce'];
        $method_name = $this->available_actions[$actionName];
        if ( NULL == $method_name ) {
            throw new ControllerException( "There is no such action" );
        }
        $this->$method_name();
    }

    protected function begin_transaction() {
        Registry::getConnection()->beginTransaction();
    }

    protected function commit() {
        Registry::getConnection()->commit();
    }

    protected function rollback() {
        Registry::getConnection()->rollBack();
    }


}

//Not sure if this will work, if not, we just need to apply it on each controller
$controller = new $this();
$controller->run();
