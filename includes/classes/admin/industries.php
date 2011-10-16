<?php

/**
 * Handles all the industries
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Industries extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
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
		if( $this->db->errno() ) {
			$this->err( 'Failed to get industries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industries;
	}
	
	/**
	 * Gets a specific industry
	 *
	 * @param int $industry_id
	 * @return array
	 */
	public function get( $industry_id ) {
		$industry = $this->db->get_row( 'SELECT `industry_id`, `name` FROM `industries` WHERE `industry_id` = ' . (int) $industry_id, ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get industry.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industry;
	}
	
	/**
	 * Gets a specific industry name
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_by_product( $product_id ) {
		$industry_name = $this->db->get_var( 'SELECT a.`name` FROM `industries` AS a LEFT JOIN `products` AS b ON ( a.`industry_id` = b.`industry_id` ) WHERE b.`product_id` = ' . (int) $product_id );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get industry by product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industry_name;
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