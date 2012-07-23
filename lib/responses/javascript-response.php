<?php

class JavascriptResponse extends CacheResponse {
    /**
     * Handle URL Redirect parameters
     *
     * @param string $file
     */
    public function __construct( $file ) {
        parent::__construct( 'js', $file );

        header::javascript();
    }
}
