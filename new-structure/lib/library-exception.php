<?php

/**
 * Base Excetion for library components
 */
class LibraryException extends Exception {

    public function __construct( $message = "", Exception $previous = NULL) {
        parent::__construct( $message, $previous );
    }

}
