<?php

class JsonResponse extends Response {
    protected $array;

    /**
     * Check if it's verified
     *
     * @param string $array
     */
    public function __construct( $array ) {
        $this->array = $array;
    }

    /**
     * Check to see if we have an error
     * @return bool
     */
    public function has_error() {
        return false;
    }

    /**
     * Spit out the html response
     */
    protected function respond() {
        // Set it to XML
        header::type('json');

        // Spit out the code and exit;
        echo json_encode( $this->array );
    }
}
