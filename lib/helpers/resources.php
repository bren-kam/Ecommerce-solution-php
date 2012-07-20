<?php
class Resources {
    /**
     * Hold the CSS
     * @var array
     */
    private $_css = array();

    /**
     * Hold the Javascript
     * @var array
     */
    private $_javascript = array();

    /**
     * Allow people to include whatever CSS files they want without duplicates
     */
	public function css() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_css ) )
                $this->_css[] = $f;
        }
	}

    /**
     * Allow people to include whatever JS files they want without duplicates
     */
	public function javascript() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_javascript ) )
                $this->_javascript[] = $f;
        }
	}


    /**
     * Get the CSS File
     *
     * @param type
     * @return string
     */
    public function get_css_file() {
        // Define the paths to check
        $paths = array( VIEW_PATH . 'css/', LIB_PATH . 'css/' );

        $files = array();

        // Find the CSS files and combine them
        foreach ( $this->_css as $file ) {
            foreach ( $paths as $path ) {
                $full_path = $path . $file;

                // Make sure it exists
                if ( is_file( $full_path ) )
                    $files[] = $full_path;
            }
        }

        // Get the cache path
        $cached_file = md5( implode( '|', $files ) ) . '.css';
        $cached_file_path = CACHE_PATH . 'css/' . $cached_file;

        // If a cache does not exist, create it, otherwise, read it
        if ( !LIVE || !file_exists( $cached_file_path ) ) {
            // Declare variables
            $css = '';

            // Combine the CSS
            foreach ( $files as $file ) {
                $css .= compress::css( file_get_contents( $file ) );
            }

            // Compress the CSS with initial settings

            // @Pedro -- this caused a fatal error, I can't figure out why it wouldn't be autoincluded
            lib('helpers/compress-css');

            $compress_css = new CompressCSS( $css, false, true );

            // Get the compressed css
            $css = $compress_css->css;

            // Write to file
            if ( $fh = @fopen( $cached_file_path, 'w' ) ) {
                fwrite( $fh, $css );
                fclose( $fh );
            }
        }

        return $cached_file;
    }

    /**
     * Get the JS File
     *
     * @param type
     * @return string
     */
    public function get_javascript_file() {
        // Define the paths to check
        $paths = array( VIEW_PATH . 'js/', LIB_PATH . 'js/' );

        $files = array();

        // Find the CSS files and combine them
        foreach ( $this->_javascript as $file ) {
            foreach ( $paths as $path ) {
                $full_path = $path . $file;

                // Make sure it exists
                if ( is_file( $full_path ) )
                    $files[] = $full_path;
            }
        }

        // Get the cache path
        $cached_file = md5( implode( '|', $files ) ) . '.js';
        $cached_file_path = CACHE_PATH . 'js/' . $cached_file;

        // If a cache does not exist, create it, otherwise, read it
        if ( !LIVE || !file_exists( $cached_file_path ) ) {
            // Declare variables
            $js = '';

            // Combine the JS
            foreach ( $files as $file ) {
                $js .= file_get_contents( $file );
            }

            $js = compress::javascript( $js );

            // Write to file
            if ( $fh = @fopen( $cached_file_path, 'w' ) ) {
                fwrite( $fh, $js );
                fclose( $fh );
            }
        }

        return $cached_file;
    }
}