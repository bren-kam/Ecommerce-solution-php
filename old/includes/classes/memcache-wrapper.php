<?php
/**
 * Memcache_Wrapper will cache and eventually use memcache
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class Memcache_Wrapper {
	/**
	 * If we're debugging Memcache shouldn't be required
	 */
	const DEBUG = true;
	
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		if ( !DEBUG ) {
			$this->mc = new Memcache;
			$this->mc->connect( "localhost", 11211 );
		}
	}
		
	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @param string $key
	 * @param string|array $value
	 * @param int $seconds the amount of seconds until expiration
	 * @param bool $compressed (optional|true)
	 * @return bool
	 */
	public function add( $key, $value, $seconds, $compressed = true ) {
		return ( DEBUG ) ? true : $this->mc->add( md5( $key ), $value, ( ( $compressed ) ? MEMCACHE_COMPRESSED : '' ), $expiration );
	}

	/**
	 * Sets data in the cache, even if it already exists
	 *
	 * @param string $key
	 * @param string|array $value
	 * @param int $seconds the amount of seconds until expiration
	 * @param bool $compressed (optional|true)
	 * @return bool
	 */
	public function set( $key, $value, $seconds, $compressed = true ) {
		return ( DEBUG ) ? true : $this->mc->set( md5( $key ), $value, ( ( $compressed ) ? MEMCACHE_COMPRESSED : '' ), $expiration );
	}

	/**
	 * Deletes item with $key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function get( $key, $compressed = true ) {
		return ( DEBUG ) ? '' : $this->mc->get( md5( $key ), ( ( $compressed ) ? MEMCACHE_COMPRESSED : '' ), $expiration );
	}

	/**
	 * Deletes item with $key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete( $key ) {
		return ( DEBUG ) ? true : $this->mc->delete( md5( $key ) );
	}

}