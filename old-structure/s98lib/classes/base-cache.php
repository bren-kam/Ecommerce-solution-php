<?php
/**
 * Base_Cache will cache variables/arrays
 *
 * Base_Cache is a Base because it is called in the base_class and 
 * we don't want recursive inheritance.
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class Base_Cache {
	/**
	 * Holds the cached objects
	 *
	 * @var array
	 * @access private
	 * @since 1.0
	 */
	private $_cache = array();

	/**
	 * Cache objects that do not exist in the cache
	 *
	 * @var array
	 * @access private
	 * @since 1.0
	 */
	private $_non_existant_objects = array();

	/**
	 * The amount of times the cache data was already stored in the cache.
	 *
	 * @since 1.0
	 * @access private
	 * @var int
	 */
	private $_cache_hits = 0;

	/**
	 * Amount of times the cache did not have the request in cache
	 *
	 * @var int
	 * @access public
	 * @since 1.0
	 */
	private $_cache_misses = 0;


	/**
	 * Retrieves the cache contents, if it exists
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $id What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *		contents on success
	 */
	public function get( $id, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( isset( $this->_cache[$group][$id] ) ) {
			$this->_cache_hits++;

			return ( is_object( $this->_cache[$group][$id] ) ) ? clone( $this->_cache[$group][$id] ) : $this->_cache[$group][$id];
		}

		if ( isset( $this->_non_existant_objects[$group][$id] ) )
			return false;

		$this->_non_existant_objects[$group][$id] = true;
		$this->_cache_misses++;

		return false;
	}

	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @uses Base_Cache->get Checks to see if the cache already has data.
	 * @uses Base_Cache->_set Sets the data after the checking the cache
	 *		contents existance.
	 *
	 * @since 1.0
	 *
	 * @param int|string $id What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @return bool False if cache ID and group already exists, true on success
	 */
	public function add( $id, $data, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

        // If it is already cached, return false
		if ( false !== $this->get( $id, $group, false ) )
			return false;

        // Set the cache
		$this->_set( $id, $data, $group );
		
		return $data;
	}

	/**
	 * Remove the contents of the cache key in the group
	 *
	 * @since 1.0
	 *
	 * @param int|string $id What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param bool $force Optional. Whether to force the unsetting of the cache
	 *		ID in the group
	 * @return bool False if the contents weren't deleted and true on success
	 */
	public function delete( $id, $group = 'default', $force = false ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( !$force && false === $this->get( $id, $group, false ) )
			return false;

        // Delete the cached item
		unset( $this->_cache[$group][$id] );

        // Store the cache miss
		$this->_non_existant_objects[$group][$id] = true;

		return true;
	}

	/**
	 * Clears the object cache of all data
	 *
	 * @since 1.0.0
	 *
	 * @return bool Always returns true
	 */
	public function flush() {
		$this->_cache = array();

		return true;
	}

	/**
	 * Replace the contents in the cache, if contents already exist
	 *
	 * @since 1.0.0
	 * @see Base_Cache->_set()
	 *
	 * @param int|string $id What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @return bool False if not exists, true if contents were replaced
	 */
	public function replace( $id, $data, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

        // If it doesn't exist, return false
		if ( false === $this->get( $id, $group, false ) )
			return false;

		return $this->_set( $id, $data, $group );
	}

	/**
	 * Sets the data contents into the cache
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $id What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @return bool Always returns true
	 */
	private function _set( $id, $data, $group = 'default' ) {
		if ( empty( $group ) )
			$group = 'default';

		if ( NULL === $data )
			$data = '';

		if ( is_object( $data ) )
			$data = clone( $data );
		
		$this->_cache[$group][$id] = $data;

		if ( isset( $this->_non_existant_objects[$group][$id] ) )
			unset( $this->_non_existant_objects[$group][$id] );

		return true;
	}
    
    /**
     * Get the misses
     *
     * @return int
     */
    public function get_misses() {
        return $this->_cache_misses;
    }

    /**
     * Get the hits
     *
     * @return int
     */
    public function get_hits() {
        return $this->_cache_hits;
    }
}