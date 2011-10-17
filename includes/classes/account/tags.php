<?php
/**
 * Handles all the tags
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Tags extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Add tags to an object
	 *
	 * @param string $type the type of tag
	 * @param int $object_id the object of whatever type of tag it is
	 * @param array $tags an array of all the tags
	 * @return array
	 */
	public function add( $type, $object_id, $tags ) {
		// Type Juggling
		$object_id = (int) $object_id;
		
		// Needs to be an array
		if ( !is_array( $tags ) )
			return false;
		
		$values = '';
		
		// Add each tag
		foreach ( $tags as $tag ) {
			$tag = trim( $tag );
			
			if ( empty( $tag ) )
				continue;
			
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $object_id, '" . $this->db->escape( $type ) . "', '" . $this->db->escape( $tag ) . "' )";
		}
		
		if ( !empty( $values ) )
			$this->db->query( "INSERT INTO `tags` ( `object_id`, `type`, `value` ) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add tags.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets all the tags for an object ID
	 *
	 * @param string $type the type of tag
	 * @param int $object_id the object of whatever type of tag it is
	 * @return bool true
	 */
	public function get( $type, $object_id ) {
		// Get tags
		$tags = $this->db->get_col( "SELECT `value` FROM `tags` WHERE `type` = '" . $this->db->escape( $type ) . "' AND `object_id` = " . (int) $object_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get tags.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $tags;
	}
	
	/**
	 * Delets all the tags associated with an object_id
	 *
	 * @param string $type the type of tag
	 * @param int $object_id the object of whatever type of tag it is
	 * @return bool true
	 */
	public function delete( $type, $object_id ) {
		// Delete tags
		$this->db->prepare( 'DELETE FROM `tags` WHERE `type` = ? AND `object_id` = ?', 'si', $type, $object_id )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete tags.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete( $query ) {
		$suggestions = $this->db->prepare( "SELECT DISTINCT( `value` ) FROM `tags` WHERE `type` = 'product' AND `value` LIKE ? ORDER BY `value` LIMIT 10", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get tag autocomplete entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}