<?php
class Resources {
    /**
     * Hold the CSS
     * @var array
     */
    private $_css = array();

    /**
     * Private CSS URLs
     *
     * @vary array
     */
    private $_css_urls = array();

    /**
     * Hold the Javascript
     * @var array
     */
    private $_javascript = array();

    /**
     * Private Javascript URLs
     *
     * @vary array
     */
    private $_javascript_urls = array();

    /**
     * Allow people to include whatever CSS files they want without duplicates
     *
     * @return Resources
     */
	public function css() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_css ) )
                $this->_css[] = $f;
        }

        return $this;
	}

    /**
     * Allow people to include whatever CSS files they want without duplicates -- before
     *
     * @return Resources
     */
	public function css_before() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_css ) )
                array_unshift( $this->_css, $f );
        }

        return $this;
	}

    /**
     * CSS URL
     *
     * @return Resources
     */
    public function css_url() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_css_urls ) )
                array_unshift( $this->_css_urls, $f );
        }

        return $this;
    }

    /**
     * Allow people to include whatever JS files they want without duplicates
     *
     * @return Resources
     */
	public function javascript() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_javascript ) )
                $this->_javascript[] = $f;
        }

        return $this;
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

        // We can take adding random files if we need to
        $css_files = func_get_args();

        if ( 0 == count( $css_files ) )
            $css_files = $this->_css;

        $files = array();

        // Find the CSS files and combine them
        foreach ( $css_files as $file ) {
            foreach ( $paths as $path ) {
                $full_path = $path . $file . '.css';

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
                //$css .= file_get_contents( $file );
            }

            // Compress the CSS with initial settings

            // @Pedro -- this caused a fatal error without the manual include as it is can't find the class,
            // I can't figure out why it wouldn't be autoincluded. Remove this line to see the error
            //lib('helpers/compress-css');

            //$compress_css = new CompressCSS( $css, false, true );

            // Get the compressed css
            //$css = $compress_css->css;

            // Write to file
            if ( $fh = fopen( $cached_file_path, 'w' ) ) {
                fwrite( $fh, $css );
                fclose( $fh );
            }
        }

        return $cached_file;
    }

    /**
     * Get CSS URLs
     *
     * @return string
     */
    public function get_css_urls() {
        if ( 0 == count( $this->_css_urls ) )
            return false;

        return '<link type="text/css" rel="stylesheet" href="' . implode( '" /><link type="text/css" rel="stylesheet" href="', $this->_css_urls ) . '" />';
    }

    /**
     * Get the JS File
     *
     * param string $arg1, $arg2.. [optional]
     * @return string
     */
    public function get_javascript_file() {
        // Compression is on by default
        $compression = false; //true;

        $paths = array( VIEW_PATH . 'js/', LIB_PATH . 'js/' );

        // We can take adding random files if we need to
        $javascript_files = func_get_args();

        if ( 0 == count( $javascript_files ) ) {
            $javascript_files = $this->_javascript;
        } else {
            $compression = false;
        }

        $files = array();

        // Find the CSS files and combine them
        foreach ( $javascript_files as $file ) {
            foreach ( $paths as $path ) {
                $full_path = $path . $file . '.js';
   
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

            if ( $compression )
                $js = compress::javascript( $js );

            // Write to file
            if ( $fh = fopen( $cached_file_path, 'w' ) ) {
                fwrite( $fh, $js );
                fclose( $fh );
            }
        }

        return $cached_file;
    }

 /**
     * Get the JSON File
     *
     * param string $arg1, $arg2.. [optional]
     * @return string
     */
    public function get_json_file() {
        // Compression is on by default
        $compression = false; //true;

        $paths = array( VIEW_PATH . 'js/', LIB_PATH . 'js/' );

        // We can take adding random files if we need to
        $javascript_files = func_get_args();

    
        if ( 0 == count( $javascript_files ) ) {
            $javascript_files = $this->_javascript;
        } else {
            $compression = false;
        }

        $files = array();

        // Find the CSS files and combine them
        foreach ( $javascript_files as $file ) {
            foreach ( $paths as $path ) {
                $full_path = $path . $file . '.json';

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

            if ( $compression )
                $js = compress::javascript( $js );

            // Write to file
            if ( $fh = fopen( $cached_file_path, 'w' ) ) {
                fwrite( $fh, $js );
                fclose( $fh );
            }
        }

        return $cached_file;
    }

/**
     * Get the JSON File
     *
     * param string $arg1, $arg2.. [optional]
     * @return string
     */
    public function get_font_file() {
        // Compression is on by default
        $compression = false; //true;

        $paths = array(  LIB_PATH . 'fonts/' );

        // We can take adding random files if we need to
        $javascript_files = func_get_args();
        
    
        if ( 0 == count( $javascript_files ) ) {
            $javascript_files = $this->_javascript;
        } else {
            $compression = false;
        }

        $files = array();

        // Find the CSS files and combine them
        foreach ( $javascript_files as $file ) {
            foreach ( $paths as $path ) {
                $full_path = $path . $file ;
                // Make sure it exists
                if ( is_file( $full_path ) )
                    return 'lib/fonts/' . $file;
            }
        }


        return false;
    }



    
    /**
     * Javascript URL
     *
     * @return Resources
     */
    public function javascript_url() {
        $files = func_get_args();

        foreach ( $files as $f ) {
            if ( !in_array( $f, $this->_javascript_urls ) )
                array_unshift( $this->_javascript_urls, $f );
        }

        return $this;
    }

    /**
     * Get Javascript URLs
     *
     * @return string
     */
    public function get_javascript_urls() {
        if ( 0 == count( $this->_javascript_urls ) )
            return false;

        return '<script src="' . implode( '"></script><script src="', $this->_javascript_urls ) . '"></script>';
    }

}
