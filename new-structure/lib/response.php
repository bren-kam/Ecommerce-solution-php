<?php

abstract class Response {

    public final function send_response() {
        if ( $this->has_error() ) {
            $this->log_error();
        }
        $this->respond();
    }

    protected function log_error() {
        //TODO Implement
    }

    /**
     * @abstract
     * @return bool
     */
    abstract protected function has_error();

    /**
     * Send a response back to browser according to mime-type
     * @abstract
     * @return void
     */
    abstract protected function respond();

}
