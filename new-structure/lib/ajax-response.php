<?php

require_once 'response.php';

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


    public function is_valid_value( $assertion, $error ) {
        if ( $assertion )
            return;

        $this->add_response( 'error', $error );
        $this->error = true;
    }

    protected  function has_error() {
        return $this->error;
    }

    protected function respond() {

        $this->add_response( 'success', ! $this->has_error() );
        if ( ! $this->has_error() ) {
            unset( $this->json_response['error'] );
        }

        // Set the header if it's not IE8 (IE8 is stupid and doesn't recognize json/application header types)
        $browser = fn::browser();

        if ( 'Msie' != $browser['name'] || version_compare( 8, $browser['version'], '>' ) )
            header::type('json');


        // Spit out the code and exit;
        echo json_encode( $this->json_response );
    }


}
