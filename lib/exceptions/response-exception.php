<?php
/**
 * Handle an exception to any Response
 */
class ResponseException extends Exception {
    public function __construct( $message = "", Exception $previous = NULL) {
        parent::__construct( $message, $previous );
    }
}
