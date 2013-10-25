<?php
class CacheResponse extends Response {
    /**
     * The cache type (i.e., css, js)
     * @var string
     */
    protected $cache_type;

    /**
     * Hold the URL to the file
     * @var string
     */
    protected $file;

    /**
     * Handle URL Redirect parameters
     *
     * @throws ResponseException
     *
     * @param string $cache_type
     * @param string $file
     */
    public function __construct( $cache_type, $file ) {
        $this->cache_type = $cache_type;
        $this->file = $file;

        $base_path = CACHE_PATH . $this->cache_type . '/';
        $real_base = realpath( $base_path );

        $user_path = $base_path . $this->file;
        $real_user_path = realpath( $user_path );

        // Stop directory traversal
        if ( $real_user_path === false || strpos( $real_user_path, $real_base ) !== 0 )
            throw new ResponseException( 'Directory Traversal Attempt' );

        // This can be overridden if necessary
        $this->path = $real_user_path;
    }

    /**
     * There is no way to have an error
     *
     * @return bool
     */
    protected function has_error() {
        return false;
    }

    /**
     * Send Header information
     */
    protected function respond() {
        readfile( $this->path );
    }
}
