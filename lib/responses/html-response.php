<?php
class HtmlResponse extends Response {
    protected $html;

    /**
     * Check if it's verified
     *
     * @param string $html
     */
    public function __construct( $html ) {
        $this->html = $html;
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
        // Spit out the code and exit;
        echo $this->html;
    }
}