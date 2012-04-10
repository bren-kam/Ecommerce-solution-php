<?php

/**
 * Handles all the industries
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Industries extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get all industries
	 *
	 * @return array
	 */
	public function get_all() {
		$industries = $this->db->get_results( 'SELECT `industry_id`, `name` FROM `industries` ORDER BY `name` ASC', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get industries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industries;
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