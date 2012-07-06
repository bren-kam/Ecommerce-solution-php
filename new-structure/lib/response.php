<?php

/**
 * Base for all responses
 */
abstract class Response {
    /**
     * Send the response
     */
    public final function send_response() {
        // Method for login errors
        if ( $this->has_error() )
            $this->log_error();

        $this->respond();
    }

    /**
     * @todo
     */
    protected function log_error() {
        // TODO Implement
    }

    /**
     * See if the response has an error
     *
     * @abstract
     * @return bool
     */
    abstract protected function has_error();

    /**
     * Send a response back to browser according to mime-type
     *
     * @abstract
     * @return void
     */
    abstract protected function respond();

}
