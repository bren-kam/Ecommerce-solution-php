<?php
class InvalidParametersException extends ModelException {
    public function __construct( $message = "", Exception $previous = NULL ) {
        parent::__construct( $message, $previous );
    }
}
