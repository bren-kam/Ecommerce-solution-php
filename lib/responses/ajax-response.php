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
     * Check if it's verified
     *
     * @param bool $verified
     */
    public function __construct( $verified ) {
        $this->check( $verified, _('A verification error occurred. Please refresh the page and try again.') );

        if ( $verified )
            lib( 'ext/jQuery/jQuery' );
    }

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
     * @return bool
     */
    public function check( $assertion, $error ) {
        if ( $assertion )
            return true;

        // Add the error response
        $this->add_response( 'error', $error );
        $this->error = true;

        return false;
    }

    /**
     * Check to see if we have an error
     * @return bool
     */
    public function has_error() {
        return $this->error;
    }

    /**
     * Spit out the json response
     */
    protected function respond() {
        // If it's not an AJAX request, refresh the current page
        if ( !fn::is_ajax() ) {
            $response = new RedirectResponse( $_SERVER['HTTP_REFERER'] );
            $response->send_response();
            return;
        }

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
