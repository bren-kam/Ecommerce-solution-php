<?php
/**
 * Handles all the Coupons
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Coupons extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Creates a coupon
	 *
	 * @param string $name
	 * @param string $code
	 * @param string $type
	 * @param float $amount
	 * @param bool $store_wide
	 * @param int $item_limit
	 * @param string $date_start
	 * @param string $date_end
	 * @param array $free_shipping_methods
	 * @return bool
	 */
	public function create( $name, $code, $type, $amount, $minimum_purchase_amount, $store_wide, $buy_one_get_one_free, $item_limit, $date_start, $date_end, $free_shipping_methods ) {
		global $user;
		
		// Checkboxes may be NULL
		if ( NULL == $store_wide )
			$store_wide = 0;
		
		if ( NULL == $buy_one_get_one_free )
			$buy_one_get_one_free = 0;
		
		// Create Coupon
		$this->db->insert( 'website_coupons', array( 'website_id' => $user['website']['website_id'], 'name' => $name, 'code' => $code, 'type' => $type, 'amount' => $amount, 'minimum_purchase_amount' => $minimum_purchase_amount, 'store_wide' => $store_wide, 'buy_one_get_one_free' => $buy_one_get_one_free, 'item_limit' => $item_limit, 'date_start' => $date_start, 'date_end' => $date_end, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isssddiiisss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create coupon.', __LINE__, __METHOD__ );
			return false;
		}
		
		$website_coupon_id = $this->db->insert_id;
		
		// Add the shipping methods
		$this->set_free_shipping_methods( $website_coupon_id, $free_shipping_methods );
		
		return $website_coupon_id;
	}
	
	/**
	 * Updates a coupon
	 *
	 * @param int $website_coupon_id
	 * @param string $name
	 * @param string $code
	 * @param string $type
	 * @param float $amount
	 * @param bool $store_wide
	 * @param int $item_limit
	 * @param string $date_start
	 * @param string $date_end
	 * @param array $free_shipping_methods
	 * @return bool
	 */
	public function update( $website_coupon_id, $name, $code, $type, $amount, $minimum_purchase_amount, $store_wide, $buy_one_get_one_free, $item_limit, $date_start, $date_end, $free_shipping_methods ) {
		global $user;
		
		// Checkboxes may be NULL
		if ( NULL == $store_wide )
			$store_wide = 0;
		
		if ( NULL == $buy_one_get_one_free )
			$buy_one_get_one_free = 0;
		
		// Create Coupon
		$this->db->update( 'website_coupons', array( 'name' => $name, 'code' => $code, 'type' => $type, 'amount' => $amount, 'minimum_purchase_amount' => $minimum_purchase_amount, 'store_wide' => $store_wide, 'buy_one_get_one_free' => $buy_one_get_one_free, 'item_limit' => $item_limit, 'date_start' => $date_start, 'date_end' => $date_end ), array( 'website_coupon_id' => $website_coupon_id, 'website_id' => $user['website']['website_id'] ), 'sssddiiiss', 'ii' );
		
		// Failed to create coupon
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update coupon.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add the shipping methods
		$this->set_free_shipping_methods( $website_coupon_id, $free_shipping_methods );
		
		return true;
	}
	
	/**
	 * Set Free Shipping Methods
	 *
	 * @param int $website_coupon_id
	 * @param array $free_shipping_methods
	 * @return bool
	 */
	public function set_free_shipping_methods( $website_coupon_id, $free_shipping_methods ) {
		// If they didn't choose anything, don't add them
		if ( !is_array( $free_shipping_methods ) )
			return true;
		
		global $user;
		
		// Type Juggling
		$website_coupon_id = (int) $website_coupon_id;
		$website_id = (int) $user['website']['website_id'];
		
		// @Fix remove the `active` column -- not needed
		
		// Delete the existing methods
		$this->db->query( "DELETE a.* FROM `website_coupon_shipping_methods` AS a LEFT JOIN `website_coupons` AS b ON ( a.`website_coupon_id` = b.`website_coupon_id` ) WHERE a.`website_coupon_id` = $website_coupon_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website coupon shipping methods.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Create values
		$values = '';
		
		foreach ( $free_shipping_methods as $website_shipping_method_id ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $website_coupon_id, " . (int) $website_shipping_method_id . ', 1 )';
		}
		
		// Create new free shipping methods
		$this->db->query( "INSERT INTO `website_coupon_shipping_methods` ( `website_coupon_id`, `website_shipping_method_id`, `active` ) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create website coupon shipping methods.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets a coupon
	 *
	 * @param int $website_coupon_id
	 * @return array
	 */
	public function get( $website_coupon_id ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		$website_coupon_id = (int) $website_coupon_id;
		
		$coupon = $this->db->get_row( "SELECT `website_coupon_id`, `name`, `code`, `type`, `amount`, `minimum_purchase_amount`, `store_wide`, `buy_one_get_one_free`, `item_limit`, DATE( `date_start` ) AS date_start, DATE( `date_end` ) AS date_end FROM `website_coupons` WHERE `website_id` = $website_id AND `website_coupon_id` = $website_coupon_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get coupon.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $coupon;
	}
	
	/**
	 * Get all coupons and return in an associative array
	 *
	 * @return array
	 */
	public function get_all() {
		global $user;
		
		$coupons = $this->db->get_results( 'SELECT `website_coupon_id`, `name`, `code`, `type`, `amount`, `store_wide`, `item_limit`, `date_start`, `date_end`, UNIX_TIMESTAMP(`date_created`) AS date_created FROM `website_coupons` WHERE `store_wide` = 0 AND `website_id` = ' . (int) $user['website']['website_id'], ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get coupons.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $coupons;
	}
	
	/**
	 * Gets the free shipping methods
	 *
	 * @param int $website_coupon_id
	 * @return array
	 */
	public function get_free_shipping_methods( $website_coupon_id ) {
		// Type Juggling
		$website_coupon_id = (int) $website_coupon_id;
		
		$free_shipping_methods = $this->db->get_col( "SELECT `website_shipping_method_id` FROM `website_coupon_shipping_methods` WHERE `website_coupon_id` = $website_coupon_id AND `active` = 1 ORDER BY `website_coupon_id` ASC" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get free shipping methods', __LINE__, __METHOD__ );
			return false;
		}
		
		return $free_shipping_methods;
	}
	
	/**
	 * List coupons
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_coupons( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$coupons = $this->db->get_results( "SELECT `website_coupon_id`, `name`, `type`, `amount`, `item_limit`, UNIX_TIMESTAMP(`date_created`) AS date_created FROM `website_coupons` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list coupons.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $coupons;
	}
	
	/**
	 * Count the coupons
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_coupons( $where ) {
		$count = $this->db->get_var(  "SELECT COUNT( `website_coupon_id` )FROM `website_coupons` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count coupons.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Delete a coupon
	 *
	 * @param int $website_coupon_id
	 * @return bool
	 */
	public function delete( $website_coupon_id ) {
		global $user;
		
		// Type Juggling
		$website_coupon_id = (int) $website_coupon_id;
		$website_id = (int) $user['website']['website_id'];
		
		$this->db->query( "DELETE FROM `website_coupons` WHERE `website_coupon_id` = $website_coupon_id AND `website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website coupon.', __LINE__, __METHOD__ );
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
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}