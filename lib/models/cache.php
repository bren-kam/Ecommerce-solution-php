<?php

/**
 * Cache
 * 
 * Interface with cache layer for data storing.
 *
 * @author gbrunacci
 */
class Cache {

	/**
	 * Get
	 * 
	 * @param string $key
	 * @return mixed, FALSE if the key is not found
	 */
	public static function get( $key ) {

		return apc_fetch( $key );
	}

	/**
	 * Set
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl expiration time in seconds, 0 to never expire. Default 0
	 * @return bool
	 */
	public static function set( $key, $value, $ttl = 0 ) {

		return apc_store( $key, $value, $ttl );
	}

	/**
	 * Exists
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public static function exists( $key ) {

		return apc_exists( $key );
	}

	/**
	 * Delete
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public static function delete( $key ) {

		return apc_delete( $key );
	}

}
