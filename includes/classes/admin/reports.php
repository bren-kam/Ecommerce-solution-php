<?php

/**
 * Handles all the report information
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Reports extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
	}
			
	/**
	 * Get all information from certain reports
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_report( $where, $order_by, $limit ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user['company_id'] : $where . ' AND b.`company_id` = ' . $user['company_id'];
		
		// What other sites we might need to omit
		$omit_sites = ( $user['role'] < 8 ) ? ', 96, 114, 115, 116' : '';
		
		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";
		
		// Get the websites
		$websites = $this->db->get_results( "SELECT a.`website_id`, a.`domain`, a.`title`, a.`products`, b.`user_id`, b.`company_id`, b.`contact_name`, b.`store_name`, SUM( IF( c.`active` = 1 OR c.`active` IS NULL, 1, 0 ) ) AS used_products FROM `websites` as a INNER JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `website_products` AS c ON ( a.`website_id` = c.`website_id` ) $where GROUP BY a.`website_id` ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get all websites.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $websites;
	}	
	
	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
	 * @return bool
	 */
	public function autocomplete( $query, $field ) {
		global $user;
		
		// Construct WHERE
		$where = ( $user['role'] < 8 ) ? ' AND b.`company_id` = ' . $user['company_id'] : '';
		
		// Get results
		$results = $this->db->prepare( "SELECT DISTINCT( a.`$field` ) FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`$field` LIKE ? $where AND a.`website_id` NOT IN ( 96, 114, 115, 116 ) ORDER BY a.`$field`", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
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