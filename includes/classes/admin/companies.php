<?php
/**
 * Handles all the companies
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Companies extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get All Companies
	 *
	 * @return array
	 */
	public function get_all() {
		$companies = $this->db->get_results( 'SELECT `company_id`, `name` FROM `companies`', ARRAY_A );
		
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to get companies', __LINE__, __METHOD__ );
			return false;
		}
		
		return $companies;
	}
	
	/**
	 * Gets the data for an autocomplete
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete( $query ) {
		global $user;
		
		// Get results
		$results = $this->db->prepare( "SELECT `company_id` AS object_id, `name` AS company FROM `companies` WHERE `name` LIKE ? ORDER BY `name`", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocomplete entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
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