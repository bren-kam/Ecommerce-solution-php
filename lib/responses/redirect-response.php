<?php

class RedirectResponse extends Response {
    /**
     * The place to which the user will be redirected
     * @var string
     */
    private $_location;

    /**
     * The HTTP Code
     * @var int
     */
    private $_code;

    /**
     * Handle URL Redirect parameters
     *
     * @param string $location
     * @param int $code [optional] 302 is Temporary Redirect
     */
    public function __construct( $location, $code = 302 ) {
        $this->_location = $location;
        $this->_code = $code;
    }

    /**
     * There is no way to have an error
     *
     * @return bool
     */
    protected function has_error() {
        return false;
    }

    /**
     * Send Header information
     */
    protected function respond() {
        url::redirect( $this->_location, $this->_code );
    }

}
