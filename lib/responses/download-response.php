<?php

class DownloadResponse extends Response {
    protected $file_name;
    protected $content;

    /**
     * Get the file name
     *
     * @param string $file_name
     * @param string $content [optional]
     */
    public function __construct( $file_name, $content = '' ) {
        $this->file_name = $file_name;

        // Set it to XML
        header::download( $this->file_name );
    }

    /**
     * Check to see if we have an error
     * @return bool
     */
    public function has_error() {
        return false;
    }

    /**
     * Spit out the json response
     */
    protected function respond() {
        echo $this->content;
    }
}
