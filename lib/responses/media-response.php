<?php

class MediaResponse extends CacheResponse {
    /**
     * Handle Images
     *
     * @throws ResponseException
     * @param string $file
     */
    public function __construct( $file ) {
        parent::__construct( 'media', basename( $file ) );

        // Set the proper path
        $this->path = VIEW_PATH . $file;

        header::type( f::extension( $file ) );
    }
}
