<?php

class AjaxResponse extends Response {
    /**
     * JSON Response
     * @var array
     */
    private $json_response = array();

    /**
     *
     * @var bool
     */
    private $error = false;

    /**
     * Add Response
     *
     * @param string $key
     * @param string $value
     */
    public function add_response( $key, $value ) {
        // Set the variable
        $this->json_response[$key] = $value;
    }

    /**
     * Checks to make sure something is not false
     *
     * @param mixed $assertion
     * @param string $error
     * @return mixed
     */
    public function check( $assertion, $error ) {
        if ( $assertion )
            return;

        // Add the error response
        $this->add_response( 'error', $error );
        $this->error = true;
    }

    /**
     * Check to see if we have an error
     * @return bool
     */
    protected function has_error() {
        return $this->error;
    }

    /**
     * Spit out the json response
     */
    protected function respond() {
        // Let them know we were sucessful
        $this->add_response( 'success', !$this->has_error() );

        // If we don't have a problem, unset it
        if ( !$this->has_error() )
            unset( $this->json_response['error'] );

        // Set it to JSON
        header::type('json');

        // Spit out the code and exit;
        echo json_encode( $this->json_response );
    }


}
