<?php
/**
 * Base Exception for library components
 */
class LibraryException extends Exception {
    /**
     * Exception for library components
     *
     * @param string $message
     * @param Exception $previous
     */
    public function __construct( $message = "", Exception $previous = NULL) {
        parent::__construct( $message, $previous );
    }
}
