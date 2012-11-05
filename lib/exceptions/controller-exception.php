<?php
/**
 * Handle an exception to any Controller
 */
class ControllerException extends Exception {
    public function __construct( $message = "", Exception $previous = NULL) {
        parent::__construct( $message, $previous );
    }
}
