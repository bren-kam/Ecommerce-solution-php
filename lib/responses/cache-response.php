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
     * @param string $cache_type
     * @param string $file
     */
    public function __construct( $cache_type, $file ) {
        $this->cache_type = $cache_type;
        $this->file = $file;
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
        readfile( CACHE_PATH . $this->cache_type . '/' . $this->file );
    }
}
