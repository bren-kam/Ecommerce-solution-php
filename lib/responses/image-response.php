<?php

class ImageResponse extends CacheResponse {
    /**
     * Handle Images
     *
     * @throws ResponseException
     * @param string $file
     */
    public function __construct( $file ) {
        parent::__construct( 'image', basename( $file ) );

        // Set the proper path
        $this->path = VIEW_PATH . $file;

        // Send the proper headers
        if ( !header::image( f::extension( $file ) ) )
            throw new ResponseException( 'Extension not recognized: ' . f::extension( $file ) );
    }
}
