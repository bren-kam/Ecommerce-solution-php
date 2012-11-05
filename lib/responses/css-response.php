<?php

class CssResponse extends CacheResponse {
    /**
     * Handle URL Redirect parameters
     *
     * @param string $file
     */
    public function __construct( $file ) {
        parent::__construct( 'css', $file );

        header::css();
    }
}
