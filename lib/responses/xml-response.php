<?php

class XmlResponse extends Response {
    protected $xml;

    /**
     * Check if it's verified
     *
     * @param string $xml
     */
    public function __construct( $xml ) {
        $this->xml = $xml;
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
        header::type('xml');

        // Spit out the code and exit;
        echo $this->xml;
    }
}
