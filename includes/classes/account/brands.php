<?php
/**
 * Handles all the Brands
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Brands extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get a brand
	 *
	 * @param int $brand_id
	 * @return array
	 */
	public function get( $brand_id ) {
		$brand = $this->db->get_row( 'SELECT * FROM `brands` WHERE `brand_id` = ' . (int) $brand_id, ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brand;
	}
	
	/**
	 * Get All brands and return in an associative array
	 *
	 * @return array
	 */
	public function get_all() {
		$brands = $this->db->get_results( 'SELECT * FROM `brands` ORDER BY `name` ASC', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brands;
	}

    /**
	 * Get All website brands and return in an associative array
	 *
	 * @return array
	 */
	public function get_website_brands() {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		$brands = $this->db->get_results( "SELECT a.* FROM `brands` AS a LEFT JOIN `products` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN `website_products` AS c ON ( b.`product_id` = c.`product_id` ) WHERE c.`website_id` = $website_id AND c.`blocked` = 0 AND c.`active` = 1 GROUP BY a.`brand_id` ORDER BY `name` ASC", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website brands.', __LINE__, __METHOD__ );
			return false;
		}

		return $brands;
	}

	/**
	 * Get a new top brand (return nothing if it's not there)
	 *
	 * @param int $brand_id
	 * @param int $sequence
	 * @return array
	 */
	public function add_top_brand( $brand_id, $sequence ) {
		global $user;
		
		// Type Juggling
		$brand_id = (int) $brand_id;
		$website_id = (int) $user['website']['website_id'];
		
		// Get the brand
		$brand = $this->db->get_row( "SELECT a.* FROM `brands` AS a LEFT JOIN `website_top_brands` AS b ON ( a.`brand_id` = b.`brand_id` AND b.`website_id` IS NULL ) WHERE a.`brand_id` = $brand_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( !$brand )
			return false;
		
		$this->db->insert( 'website_top_brands', array( 'website_id' => $website_id, 'brand_id' => $brand_id, 'sequence' => $sequence ), 'iii' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add website top brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brand;
	}

	/**
	 * Get website brands 
	 *
	 * @return array
	 */
	public function get_top_brands() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$brands = $this->db->get_results( "SELECT a.* FROM `brands` AS a LEFT JOIN `website_top_brands` AS b ON ( a.`brand_id` = b.`brand_id` ) WHERE b.`website_id` = $website_id ORDER BY b.`sequence` ASC", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get top brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brands;
	}
	
	/**
	 * Update brand sequence
	 *
	 * @param array $sequence
	 * @return bool
	 */
	public function update_sequence( array $sequence ) { // Type Hinting (Second one EVER)
		global $user;
		 
		// Type Juggle
		$website_id = (int) $user['website']['website_id'];

		// Prepare statement
		$statement = $this->db->prepare( "UPDATE `website_top_brands` SET `sequence` = ? WHERE `brand_id` = ? AND `website_id` = $website_id" );
		$statement->bind_param( 'ii', $count, $brand_id );
		
		foreach ( $sequence as $count => $brand_id ) {
			$statement->execute();
			
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->_err( 'Failed to update brand sequence', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Gets the data for an autocomplete
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete( $query ) {
		$suggestions = $this->db->get_results( "SELECT `brand_id` AS value, `name` FROM `brands` WHERE `name` LIKE '" . $this->db->escape( $query ) . "%' ORDER BY `name` LIMIT 10", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get autocompleted brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Gets the data for an autocomplete request for custom products
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete_custom( $query ) {
		global $user;
		
		$suggestions = $this->db->get_results( "SELECT a.`brand_id` AS value, a.`name` FROM `brands` AS a LEFT JOIN `products` AS b ON ( a.`brand_id` = b.`brand_id` ) WHERE a.`name` LIKE '" . $this->db->escape( $query ) . "%' AND b.`website_id` = " . (int) $user['website']['website_id'] . " ORDER BY `name` LIMIT 10", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get autocompleted brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Gets the data for an autocomplete request for owned items
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete_owned( $query ) {
		global $user;
		
		// @Fix do we need to support the non deprecated method as well?
		// Deprecated, but needed for old files that are still like this
		
		$suggestions = $this->db->get_results( "SELECT DISTINCT a.`name` FROM `brands` AS a LEFT JOIN `products` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN `website_products` AS c ON ( b.`product_id` = c.`product_id` ) WHERE a.`name` LIKE '" . $this->db->escape( $query ) . "%' AND c.`website_id` = " . (int) $user['website']['website_id'] . " ORDER BY a.`name` LIMIT 10", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get autocompleted brands on owned products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Remove Brand
	 *
	 * @param int $brand_id
	 * @return bool
	 */
	public function remove( $brand_id ) {
		global $user;
		
		// Type Juggling
		$brand_id = (int) $brand_id;
		$website_id = (int) $user['website']['website_id'];
		
		$this->db->query( "DELETE FROM `website_top_brands` WHERE `brand_id` = $brand_id AND `website_id` = $website_id" );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to remove website top brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}