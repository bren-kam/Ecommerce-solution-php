<?php
/**
 * Handle an exception to any Controller
 */
class ModelException extends Exception {
    public function __construct( $message = "", Exception $previous = NULL, $code = 0 ) {
        parent::__construct( $message, $code, $previous );
    }
}
