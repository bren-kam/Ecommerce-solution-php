<?php
class TextResponse extends Response {
    protected $text;

    /**
     * Check if it's verified
     *
     * @param string $text
     */
    public function __construct( $text ) {
        $this->text = $text;
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
        // Set it to text
        header::type('text');

        // Spit out the code and exit;
        echo $this->text;
    }
}