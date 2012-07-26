<?php
class ModelException extends Exception {
    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct( $message = "", Exception $previous = NULL) {
        parent::__construct( $message, $previous );
    }
}
