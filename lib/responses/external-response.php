<?php

class ExternalResponse extends CacheResponse {
    /**
     * Handle things external to the system (i.e. ckeditor)
     *
     * @throws ResponseException
     * @param string $file
     */
    public function __construct( $file ) {
        parent::__construct( 'external', basename( $file ) );


        // Set the proper path
        $this->path = str_replace( '?' . $_SERVER['QUERY_STRING'], '', ABS_PATH . $file );

        header::type( f::extension( $this->path ) );
    }
}
