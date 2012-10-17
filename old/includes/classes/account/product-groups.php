<?php
/**
 * Handles all the Product Groups
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Product_Groups extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Gets a product group
	 *
	 * @param int $website_product_group_id
	 * @return array
	 */
	public function get( $website_product_group_id ) {
		global $user;
		
		// Type Juggling
		$website_product_group_id = (int) $website_product_group_id;
		$website_id = $user['website']['website_id'];
		
		$group = $this->db->get_row( "SELECT `website_product_group_id`, `name` FROM `website_product_groups` WHERE `website_product_group_id` = $website_product_group_id AND `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get product group.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $group;
	}
	
	/**
	 * Gets a products for a product group
	 *
	 * @param int $website_product_group_id
	 * @return array
	 */
	public function get_products( $website_product_group_id ) {
		global $user;
		
		// Type Juggling
		$website_product_group_id = (int) $website_product_group_id;
		$website_id = $user['website']['website_id'];
		
		$product_ids = $this->db->get_col( "SELECT a.`product_id` FROM `website_product_group_relations` AS a LEFT JOIN `website_product_groups` AS b ON ( a.`website_product_group_id` = b.`website_product_group_id` ) WHERE a.`website_product_group_id` = $website_product_group_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get product group products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_ids;
	}
	
	/**
	 * Create Product Group
	 *
	 * @param string $name
	 * @param array $products
	 * @return int
	 */
	public function create( $name, $products ) {
		global $user;

		// Create Coupon
		$this->db->insert( 'website_product_groups', array( 'website_id' => $user['website']['website_id'], 'name' => $name ), 'is' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		$website_product_group_id = $this->db->insert_id;
		
		// Add the products
		$this->set_products( $website_product_group_id, $products );
		
		return $website_product_group_id;
	}
	
	/**
	 * Set Products
	 *
	 * @param int $website_product_group_id
	 * @param array $products
	 * @return bool
	 */
	public function set_products( $website_product_group_id, $products ) {
		// If they didn't choose anything, don't add them
		if ( !is_array( $products ) )
			return true;
		
		global $user;
		
		// Type Juggling
		$website_product_group_id = (int) $website_product_group_id;
		$website_id = (int) $user['website']['website_id'];
		
		// Delete the existing products
		$this->db->query( "DELETE a.* FROM `website_product_group_relations` AS a LEFT JOIN `website_product_groups` AS b ON ( a.`website_product_group_id` = b.`website_product_group_id` ) WHERE a.`website_product_group_id` = $website_product_group_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete website product group products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Create values
		$values = '';
		
		foreach ( $products as $product_id ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $website_product_group_id, " . (int) $product_id . ' )';
		}
		
		// Create new product group relations
		$this->db->query( "INSERT INTO `website_product_group_relations` ( `website_product_group_id`, `product_id` ) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add website product group relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Create Product Group
	 *
	 * @param string $name
	 * @param array $products
	 * @return bool
	 */
	public function update( $website_product_group_id, $name, $products ) {
		global $user;
		
		// Create Coupon
		$this->db->update( 'website_product_groups', array( 'name' => $name ), array( 'website_product_group_id' => $website_product_group_id, 'website_id' => $user['website']['website_id'] ), 's', 'ii' );
		
		// Failed to create coupon
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add the products
		$this->set_products( $website_product_group_id, $products );
		
		return true;
	}

	/**
	 * List groups
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_groups( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$groups = $this->db->get_results( "SELECT `website_product_group_id`, `name` FROM `website_product_groups` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list product groups.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $groups;
	}
	
	/**
	 * Count the groups
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_groups( $where ) {
		$count = $this->db->get_var(  "SELECT COUNT( `website_product_group_id` ) FROM `website_product_groups` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count website products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Get Names
	 *
	 * @param return array
	 */
	public function get_names( $website_product_group_id ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		$website_product_group_id = (int) $website_product_group_id;
		
		$names = $this->db->get_col( "SELECT a.`name` FROM `products` AS a LEFT JOIN `website_product_group_relations` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `website_product_groups` AS c ON ( b.`website_product_group_id` = c.`website_product_group_id` ) WHERE c.`website_product_group_id` = $website_product_group_id AND c.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website product group names.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $names;
	}
	
	/**
	 * Delete a group
	 *
 	 * @param int $website_product_group_id
	 * @return array
	 */
	public function delete( $website_product_group_id ) {
		global $user;
		
		// Type Juggling
		$website_product_group_id = (int) $website_product_group_id;
		$website_id = (int) $user['website']['website_id'];
		
		// Dlete from website product group relations
		$this->db->query( "DELETE a.* FROM `website_product_group_relations` AS a LEFT JOIN `website_product_groups` AS b ON ( a.`website_product_group_id` = b.`website_product_group_id` ) WHERE a.`website_product_group_id` = $website_product_group_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete website product group relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		$this->db->query( "DELETE FROM `website_product_groups` WHERE `website_product_group_id` = $website_product_group_id AND `website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete website product group.', __LINE__, __METHOD__ );
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
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}