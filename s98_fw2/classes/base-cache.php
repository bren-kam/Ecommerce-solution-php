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
	private $cache = array ();

	/**
	 * Cache objects that do not exist in the cache
	 *
	 * @var array
	 * @access private
	 * @since 1.0
	 */
	private $non_existant_objects = array ();

	/**
	 * The amount of times the cache data was already stored in the cache.
	 *
	 * @since 1.0
	 * @access private
	 * @var int
	 */
	private $cache_hits = 0;

	/**
	 * Amount of times the cache did not have the request in cache
	 *
	 * @var int
	 * @access public
	 * @since 1.0
	 */
	public $cache_misses = 0;

	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @uses Base_Cache->get Checks to see if the cache already has data.
	 * @uses Base_Cache->set Sets the data after the checking the cache
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
		if( empty( $group ) )
			$group = 'default';

		if( false !== $this->get( $id, $group, false ) )
			return false;
		
		$this->set( $id, $data, $group );
		
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
		if( empty( $group ) )
			$group = 'default';

		if( !$force && false === $this->get( $id, $group, false ) )
			return false;

		unset( $this->cache[$group][$id] );
		$this->non_existant_objects[$group][$id] = true;
		return true;
	}

	/**
	 * Clears the object cache of all data
	 *
	 * @since 1.0.0
	 *
	 * @return bool Always returns true
	 */
	function flush() {
		$this->cache = array();

		return true;
	}

	/**
	 * Retrieves the cache contents, if it exists
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $id What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *		contents on success
	 */
	function get( $id, $group = 'default' ) {
		if( empty( $group ) )
			$group = 'default';

		if( isset( $this->cache[$group][$id] ) ) {
			$this->cache_hits++;
			
			return ( is_object( $this->cache[$group][$id] ) ) ? bc_clone( $this->cache[$group][$id] ) : $this->cache[$group][$id];
		}

		if ( isset( $this->non_existant_objects[$group][$id] ) )
			return false;

		$this->non_existant_objects[$group][$id] = true;
		$this->cache_misses++;
		return false;
	}

	/**
	 * Replace the contents in the cache, if contents already exist
	 *
	 * @since 1.0.0
	 * @see Base_Cache->set()
	 *
	 * @param int|string $id What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @return bool False if not exists, true if contents were replaced
	 */
	function replace( $id, $data, $group = 'default' ) {
		if( empty( $group ) )
			$group = 'default';

		if( false === $this->get( $id, $group, false ) )
			return false;

		return $this->set( $id, $data, $group );
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
	function set( $id, $data, $group = 'default' ) {
		if( empty( $group ) )
			$group = 'default';

		if( NULL === $data )
			$data = '';

		if ( is_object( $data ) )
			$data = clone( $data );
		
		$this->cache[$group][$id] = $data;

		if( isset( $this->non_existant_objects[$group][$id] ) )
			unset( $this->non_existant_objects[$group][$id] );

		return true;
	}
}