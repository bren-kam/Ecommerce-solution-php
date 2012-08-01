<?php
/**
 * Handle an exception to any Controller
 */
class ModelException extends Exception {
    public function __construct( $message = "", Exception $previous = NULL ) {
        parent::__construct( $message, 0, $previous );
    }
}
