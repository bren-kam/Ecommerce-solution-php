<?php
/**
 * Handle an exception to any Controller
 */
class ModelException extends Exception {
    public function __construct( $message = "", $code = 0, Exception $previous = NULL ) {
        parent::__construct( $message, (int) $code, $previous );
    }
}
