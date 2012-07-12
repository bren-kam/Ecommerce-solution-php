<?php
/**
 * Handles all the craiglist functions
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Shopping_Cart extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Gets all the users
	 * 
	 * @param int $website_id
	 * @return array
	 */
	public function get_users( $website_id ) {
		$users = $this->db->prepare( "SELECT `website_user_id`, `email`, `billing_first_name`, `shipping_first_name`, `status`, UNIX_TIMESTAMP( `date_registered` ) AS date_registered FROM `website_users` WHERE `website_id` = ? ORDER BY `email`", 'i', $website_id )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart users', __LINE__, __METHOD__ );
			return false;
		}
	
		return $users;
	}
	
	/**
	 * List users
	 * 
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_users( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$users = $this->db->get_results( "SELECT `website_user_id`, `email`, `billing_first_name`, `status`, UNIX_TIMESTAMP( `date_registered` ) AS date_registered FROM `website_users` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list shopping cart users', __LINE__, __METHOD__ );
			return false;
		}
	
		return $users;
	}
	
	/**
	 * Count users
	 *
	 * @param $where
	 * @return array
	 */
	public function count_users( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `website_user_id` ) FROM `website_users` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count users', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Gets all of the information for a specific user
	 *
	 * @param int $website_user_id
	 * @return array|bool
	 */
	public function get_user( $website_user_id, $website_id ) {
		$user = $this->db->prepare( "SELECT `website_user_id`, `email`, `billing_first_name`, `billing_last_name`, `billing_address1`, `billing_address2`, `billing_city`, `billing_state`, `billing_phone`, `billing_alt_phone`, `billing_zip`, `shipping_first_name`, `shipping_last_name`, `shipping_address1`, `shipping_address2`, `shipping_city`, `shipping_state`, `shipping_zip`, `status`, `date_registered` FROM `website_users` WHERE `website_user_id` = ? AND `website_id` = " . (int) $website_id, 'i', $website_user_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart user', __LINE__, __METHOD__ );
			return false;
		}
	
		return $user;
	}
	
	/**
	 * Edits a website user
	 *
	 * @param int $website_user_id
	 * @param string $email
	 * @param string $password
	 * @param string $billing_first_name
	 * @param string $billing_last_name
	 * @param string $billing_address1
	 * @param string $billing_address2
	 * @param string $billing_city
	 * @param string $billing_state
	 * @param string $billing_zip
	 * @param string $shipping_first_name
	 * @param string $shipping_last_name
	 * @param string $shipping_address1
	 * @param string $shipping_address2
	 * @param string $shipping_city
	 * @param string $shipping_state
	 * @param string $shipping_zip
	 * @param int $status
	 * @return bool
	 */
	public function edit_user( $website_user_id, $email, $password, $billing_first_name, $billing_last_name, $billing_address1, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_phone, $billing_alt_phone, $shipping_first_name, $shipping_last_name, $shipping_address1, $shipping_address2, $shipping_city, $shipping_state, $shipping_zip, $status ) {		
		$sql_password = ( empty( $password ) ) ? '' : md5( $password );
		
		$this->db->update( 'website_users', 
						   array(
							'email' => $email,
							'password' => $sql_password, 
							'billing_first_name' => $billing_first_name, 
							'billing_last_name' => $billing_last_name, 
							'billing_address1' => $billing_address1, 
							'billing_address2' => $billing_address2, 
							'billing_city' => $billing_city, 
							'billing_state' => $billing_state, 
							'billing_zip' => $billing_zip,
							'billing_phone' => $billing_phone,
							'billing_alt_phone' => $billing_alt_phone,
							'shipping_first_name' => $shipping_first_name, 
							'shipping_last_name' => $shipping_last_name, 
							'shipping_address1' => $shipping_address1, 
							'shipping_address2' => $shipping_address2, 
							'shipping_city' => $shipping_city, 
							'shipping_state' => $shipping_state, 
							'shipping_zip' => $shipping_zip, 
							'status' => $status
							), 
						   array( 'website_user_id' => $website_user_id ),
						   'ssssssssssssssssssi', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to edit shopping cart users', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Deletes a user
	 *
	 * @param int $website_user_id
	 * @return bool
	 */
	public function delete_user( $website_user_id, $website_id ) {
		$this->db->query( "DELETE FROM `website_users` WHERE `website_user_id` = " . (int)$website_user_id . " AND `website_id` = " . (int) $website_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart users', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Checks an email for availability
	 *
	 * @param string $email
	 * @return bool
	 */
	public function check_email( $website_id, $email ) {
		$row = $this->db->prepare( "SELECT `website_user_id` FROM `website_users` WHERE `website_id` = " . (int) $website_id . " AND `email` = ?", 's', $email )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to check shopping cart email', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( $row ) ? false : true;
	}
	
	/**
	 * Adds a website user
	 *
	 * @param string $website_id
	 * @param string $email
	 * @param string $password
	 * @param string $billing_first_name
	 * @param string $billing_last_name
	 * @param string $billing_address1
	 * @param string $billing_address2
	 * @param string $billing_city
	 * @param string $billing_state
	 * @param string $billing_zip
	 * @param string $billing_phone
	 * @param string $billing_alt_phone
	 * @param string $shipping_first_name
	 * @param string $shipping_last_name
	 * @param string $shipping_address1
	 * @param string $shipping_address2
	 * @param string $shipping_city
	 * @param string $shipping_state
	 * @param string $shipping_zip
	 * @param int $status
	 * @return bool
	 */
	public function add_user( $website_id, $email, $password, $billing_first_name, $billing_last_name, $billing_address1, $billing_address2, $billing_city, $billing_state, $billing_zip, $billing_phone, $billing_alt_phone, $shipping_first_name, $shipping_last_name, $shipping_address1, $shipping_address2, $shipping_city, $shipping_state, $shipping_zip, $status ) {		
		$sql_password = ( empty( $password ) ) ? '' : md5( $password );
		
		$this->db->insert( 'website_users', 
			array( 
				  'website_id' => $website_id,
				  'email' => $email,
				  'password' => $sql_password,
				  'billing_first_name' => $billing_first_name,
				  'billing_last_name' => $billing_last_name,
				  'billing_address1' => $billing_address1,
				  'billing_address2' => $billing_address2,
				  'billing_city' => $billing_city,
				  'billing_state' => $billing_state, 
				  'billing_zip' => $billing_zip,
				  'billing_phone' => $billing_phone,
				  'billing_alt_phone' => $billing_alt_phone,
				  'shipping_first_name' => $shipping_first_name, 
				  'shipping_last_name' => $shipping_last_name, 
				  'shipping_address1' => $shipping_address1, 
				  'shipping_address2' => $shipping_address2, 
				  'shipping_city' => $shipping_city, 
				  'shipping_state' => $shipping_state, 
				  'shipping_zip' => $shipping_zip, 
				  'status' => $status
			  ),
			'issssssssssssssssssi' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add shopping cart user', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets the orders
	 * 
	 * @param array $variables ( 'where', 'order_by', 'limit' )
	 * @return array|bool
	 */
	public function get_orders( $variables ) {
		list( $where, $order_by, $limit ) = $variables;
				
		// $orders = $this->db->query( sprintf( "SELECT `website_order_id`, `total_cost`, `status`, UNIX_TIMESTAMP( `date_created` ) AS date_created FROM `website_orders` WHERE `website_id` = %d $order_by", $_SESSION['website']['website_id'] ) )->result_array( FALSE );
		//$orders = $this->db->prepare( "SELECT `website_order_id`, `total_cost`, `status`, UNIX_TIMESTAMP( `date_created` ) AS date_created FROM `website_orders` WHERE `website_id` = $website_id ORDER BY $order_by $limit_sql" )->get_results('', ARRAY_A );
		$orders = $this->db->get_results( "SELECT `website_order_id`, `total_cost`, `status`, UNIX_TIMESTAMP( `date_created` ) AS date_created FROM `website_orders` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart orders', __LINE__, __METHOD__ );
			return false;
		}
		
		return $orders;
	}
	
	/**
	 * Gets all the information for a specific order
	 * 
	 * @param int $website_order_id
	 * @param int $website_id
	 * @return array|bool
	 */
	public function get_order( $website_order_id, $website_id ) {
		$order = $this->db->get_row( "SELECT a.*, b.`name` AS shipping_method FROM `website_orders` AS a LEFT JOIN `website_shipping_methods` AS b ON ( a.`website_shipping_method_id` = b.`website_shipping_method_id` ) WHERE a.`website_order_id` = " . (int)$website_order_id . " AND a.`website_id` = " . (int)$website_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart order', __LINE__, __METHOD__ );
			return false;
		}
		
		$items = $this->db->get_results( "SELECT a.*, c.`name` AS industry, d.`image`, d.`swatch` FROM `website_order_items` AS a INNER JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) INNER JOIN `industries` AS c ON ( b.`industry_id` = c.`industry_id` ) INNER JOIN ( SELECT `product_id`, `image`, `swatch` FROM `product_images` WHERE `sequence` = 0 ) AS d ON ( a.`product_id` = d.`product_id` ) WHERE a.`website_order_id` = " . (int)$website_order_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart order items', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there are items
		if ( array( $items ) && count( $items ) > 0 ) {
			// Populate local variables
			foreach ( $items as $item ) {
				$swatch = ( !empty( $item['swatch'] ) ) ? $item['swatch'] . '/' : '';
				$image_link = 'http://' . $item['industry'] . '.retailcatalog.us/products/' . $item['product_id'] . '/' . $swatch . $item['image'];
				
				if ( isset( $order['items'][$item['website_order_item_id']]['swatch'] ) )
					unset( $order['items'][$item['website_order_item_id']]['swatch'] );

				$order['items'][$item['website_order_item_id']] = $item;
				$order['items'][$item['website_order_item_id']]['image'] = $image_link;
				$order['items'][$item['website_order_item_id']]['product_options'] = $this->get_product_options( $item['website_order_item_id'] );
				$order['items'][$item['website_order_item_id']]['extra'] = unserialize( $item['extra'] );
			}
		}
		
		return $order;
	}
	
	/**
	 * Gets the count of total orders
	 * 
	 * @param array $website_id
	 * @return array|bool
	 */
	public function count_orders( $website_id ) {				
		$orders = $this->db->get_var( "SELECT COUNT(`website_order_id`) FROM `website_orders` WHERE `website_id` = " . (int)$website_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count shopping cart orders', __LINE__, __METHOD__ );
			return false;
		}
		
		return $orders;
	}
	
	/**
	 * Adds a shipping method
	 *
	 * @param string %type
	 * @param string $name shipping method name
	 * @param string $method the type of shipping method (Flat Rate, Percentage, etc.)
	 * @param float $amount (the amount that will be charged)
	 * @param string $extra (optional|)
	 * @return int|bool
	 */
	public function add_shipping_method( $type, $name, $method, $amount, $extra = '' ) {
		global $user;
		
		if ( !empty( $extra ) )
			$extra = serialize( $extra );
		
		$this->db->insert( 'website_shipping_methods', array( 'website_id' => $user['website']['website_id'], 'type' => $type, 'name' => $name, 'method' => $method, 'amount' => $amount, 'extra' => $extra, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isssdss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add shipping method', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Delete's a shipping method
	 *
	 * @param int $website_shipping_method_id
	 * @return bool
	 */
	public function delete_shipping_method( $website_shipping_method_id ) {
		global $user;
		$this->db->query( "DELETE FROM `website_shipping_methods` WHERE `website_shipping_method_id` = " . (int)$website_shipping_method_id . " AND `website_id` = " . (int) $user['website']['website_id'] );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete shipping method', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update shipping zips
	 *
	 * @param int $website_shipping_method_id
	 * @param array $zip_codes
	 * @return int|bool
	 */
	public function update_shipping_zip_codes( $website_shipping_method_id, $zip_codes ) {
		global $user;
		$zip_codes = ( 0 == count( $zip_codes ) ) ? '' : serialize( $zip_codes );
		
		// $this->db->query( sprintf( "UPDATE `website_shipping_methods` SET `zip_codes` = '%s' WHERE `website_id` = %d AND `website_shipping_method_id` = %d", mysql_real_escape_string( $zip_codes ), $_SESSION['website']['website_id'], $website_shipping_method_id ) );
		$this->db->update( 'website_shipping_methods',
						  array( 'zip_codes' => $zip_codes ),
						  array( 'website_id' => $user['website']['website_id'],
								 'website_shipping_method_id' => $website_shipping_method_id
								 ),
						  's', 'ii' );
								 
																  
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shipping method zip codes', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_shipping_method_id;
	}
	
	/**
	 * Get shipping zips
	 *
	 * @param int $website_shipping_method_id
	 * @return array|bool
	 */
	public function get_shipping_zip_codes( $website_shipping_method_id ) {
		global $user;
		$row = $this->db->get_var( "SELECT `zip_codes` FROM `website_shipping_methods` WHERE `website_id` = " . (int)$user['website']['website_id'] . " AND `website_shipping_method_id` = " . (int)$website_shipping_method_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete shipping method', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( empty( $row ) ) ? '' : unserialize( $row );
	}
	
	/**
	 * Get a shipping method
	 *
	 * @param int $website_shipping_method_id
	 * @return array
	 */
	public function get_shipping_method( $website_shipping_method_id ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		$website_shipping_method_id = (int) $website_shipping_method_id;
		
		$shipping_method = $this->db->get_row( "SELECT `website_shipping_method_id`, `type`, `name`, `method`, `amount`, `extra` FROM `website_shipping_methods` WHERE `website_id` = $website_id AND `website_shipping_method_id` = $website_shipping_method_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shipping method', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( 'custom' != $shipping_method['type'] )
			$shipping_method['extra'] = unserialize( $shipping_method['extra'] );
		
		return $shipping_method;
	}
	
	/**
	 * Get shipping methods
	 *
	 * @return array
	 */
	public function get_shipping_methods() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$shipping_methods = $this->db->get_results( "SELECT `website_shipping_method_id`, `name`, `method`, `amount` FROM `website_shipping_methods` WHERE `website_id` = $website_id ORDER BY `date_created` ASC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shipping methods', __LINE__, __METHOD__ );
			return false;
		}
		
		return $shipping_methods;
	}
	
	/**
	 * List shipping methods
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_shipping_methods( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$shipping_methods = $this->db->get_results( "SELECT `website_shipping_method_id`, `type`, `name`, `method`, `amount` FROM `website_shipping_methods` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list shipping methods', __LINE__, __METHOD__ );
			return false;
		}
		
		return $shipping_methods;
	}
	
	/**
	 * Count shipping methods
	 *
	 * @param $where
	 * @return array
	 */
	public function count_shipping_methods( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `website_shipping_method_id` ) FROM `website_shipping_methods` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count shipping methods', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Update shipping method
	 *
	 * @param int $website_shipping_method_id
	 * @param string $name
	 * @param string $method the type of shipping method (Flat Rate, Percentage, etc.)
	 * @param float $amount the amount that will be charged
	 * @param string $extra (optional|)
	 * @return bool
	 */
	public function update_shipping_method( $website_shipping_method_id, $name, $method, $amount, $extra = '' ) {
		global $user;
		
		// Handle any extra information
		if ( !empty( $extra ) )
			$extra = serialize( $extra );
		
		$this->db->update( 'website_shipping_methods', array( 'name' => $name, 'method' => $method, 'amount' => $amount, 'extra' => $extra ), array( 'website_id' => $user['website']['website_id'], 'website_shipping_method_id' => $website_shipping_method_id ), 'ssss', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update shipping method', __LINE__, __METHOD__ );
			return false;
		}
				
		return true;
	}
	
	/**
	 * Get product options for an order item
	 *
	 * @param int $website_order_item_id
	 * @return array|bool
	 */
	private function get_product_options( $website_order_item_id ) {	
		$product_options = $this->db->get_results( "SELECT `product_option_id`, `product_option_list_item_id`, `price`, `option_type`, `option_name`, `list_item_value` FROM `website_order_item_options` WHERE `website_order_item_id` = " . (int)$website_order_item_id . " ORDER BY `option_type` DESC", ARRAY_A );
	
	// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get shopping cart product options', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_options;
	}
	
	/**
	 * Update order status
	 *
	 * @param int $website_order_id
	 * @param int $status
	 * @return int|bool
	 */
	public function update_order_status( $website_order_id, $status ) {
		$this->db->update( 'website_orders', array( 'status' => $status ), array( 'website_order_id' => $website_order_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update order status', __LINE__, __METHOD__ );
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

/************ EVERYTHING BELOW HERE APPLIES ONLY TO CRAIGSLIST!  DELETE AT WILL!!!! **********/

	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query the query that was given
	 * @param string $field the field that is needed
	 * @param int $website_id the id of the website being searched for
	 * @return array
	 */
	public function autocomplete( $query, $field, $website_id ) {
		$results = $this->db->get_results( "SELECT DISTINCT a.`$field` FROM `products` AS a LEFT JOIN `website_industries` as b ON ( a.`industry_id` = b.`industry_id` ) LEFT JOIN `website_products` AS c ON ( a.`product_id` = c.`product_id` ) WHERE ( a.`website_id` = 0 || a.`website_id` = $website_id ) AND a.`publish_visibility` = 'public' AND b.`website_id` = $website_id AND c.`website_id` = $website_id AND `$field` LIKE '$query%' ORDER BY `$field`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to perform autocomplete', __LINE__, __METHOD__ );
			return false;
		}
				
		return $results;
	}
	
	/**
	 * Gets craigslist ads
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array $craigslist_ads
	 */
	public function get_craigslist_ads( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$craigslist_ads = $this->db->get_results( "SELECT a.`title`, a.`craigslist_ad_id`, a.`text`, a.`duration`, 
												 c.`name` AS `product_name`, c.`sku`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, UNIX_TIMESTAMP( a.`date_posted` ) AS date_posted 
												 FROM `craigslist_ads` AS a 
												 LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) 
												 WHERE a.`active` = '1' $where GROUP BY a.`craigslist_ad_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist_ads;
	}
	
	/**
	 * Gets a single ad
	 *
	 * @param int $craigslist_ad_id
	 * @return array
	 */
	public function get( $craigslist_ad_id ) {
		$results = $this->db->prepare( "SELECT a.`title`, a.`craigslist_ad_id`, a.`text`, a.`duration`, a.`product_id`,
									  			 b.`title` AS `store_name`,
												 c.`name` AS `product_name`, c.`sku`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, UNIX_TIMESTAMP( a.`date_posted` ) AS date_posted 
												 FROM `craigslist_ads` AS a 
												 LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) 
												 LEFT JOIN `products` AS c ON ( a.product_id = c.product_id ) 
												 WHERE a.`craigslist_ad_id` = ? LIMIT 1", 'i', $craigslist_ad_id )->get_row('', ARRAY_A);
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Count the number of templates for a particular category
	 *
	 * @param int $category_id
	 * @return int number of ads.
	 */
	public function count_templates_for_category( $category_id ){
		$results = $this->db->prepare( "SELECT COUNT(`craigslist_template_id`) FROM `craigslist_templates` WHERE `category_id` = ? AND `publish_visibility` = 'visible'", 'i', $category_id )->get_var( '' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count craigslist templates.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Gets a single craigslist ad templates
	 *
	 * @param int $category_id
	 * @param int $direction
	 * @param int $start
	 * @param string $order
	 * @return template
	 */
	public function get_template( $category_id, $direction, $start, $order){		
		$start = intval( $start );
		
		switch ( $direction ){
			case 1:
				$where = " a.`craigslist_template_id` > $start ";
				$order = " ASC";
				break;
			
			case -1:
				$where = " a.`craigslist_template_id` < $start ";
				$order = " DESC";
				break;
				
			default:
				$where = " a.`craigslist_template_id` = $start ";
				$order = " ASC";
				break;
		}
	  
		
		$results = $this->db->prepare( "
						 SELECT a.`craigslist_template_id`, a.`title`, a.`description`, a.`category_id`, b.`name` AS `category_name` 
						 FROM `craigslist_templates` AS a LEFT JOIN `categories` AS b ON (a.`category_id` = b.`category_id`) 
						 WHERE ( " . $where . " ) AND a.`category_id` = ? ORDER BY a.`craigslist_template_id` " . $order . " LIMIT 1
						 "
						, 'i' , $category_id )->get_row('', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get craigslist template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Countscraigslist ads
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_craigslist_ads( $where ) {
		// @Fix need to make this count without PHP's count
		$craigslist_ad_ids = $this->db->get_results( "SELECT a.`craigslist_ad_id`
												 FROM `craigslist_ads` AS a 
												 LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) 
												 WHERE a.`active` = '1' $where GROUP BY a.`craigslist_ad_id`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return count( $craigslist_ad_ids );
	}
	
	/**
	 * Gets the product_id searched by a criterion
	 *
	 * @param string $search_by
	 * @param string $query
	 * @return int product id
	 */
	public function get_product_id( $search_by, $query )
	{
		if ( !$search_by || !$query) return false;
		
		switch ( $search_by ) {
			case 'sku':
				$search_by = 'sku';
				break;
			case 'product_name':
				$search_by = 'name';
			break;
			default:
				return false;
			break;
		}

		$result = $this->db->prepare( "SELECT `product_id` FROM `products` WHERE `$search_by` = ?", 's', $query )->get_var( '' );
		//echo "|$result|";
		//exit;
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get product id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $result;
	}
		
	/**
	 * Gets a single product of product_id.
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product( $product_id )
	{
		$result = $this->db->prepare( "
						 SELECT a.`description`, d.`name` as `brand`, a.`product_id`, a.`name` AS `product_name`, c.`category_id`, c.`name` AS `category_name`, a.`sku`, a.`product_specifications`
						 FROM `products` AS a 
						 INNER JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` )
						 LEFT JOIN `categories` AS c ON ( b.`category_id` = c.`category_id` )
						 LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` )
						 WHERE ( a.`product_id` = $product_id ) LIMIT 1
						 "
				, 'i', $product_id )->get_row('', ARRAY_A);
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get product info.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $result;
	}
	
	/**
	 * Retrieves the partial URLs of all images for a given Product_id
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product_image_urls( $product_id ) {
		$results = $this->db->get_col( "SELECT CONCAT( 'http://', b.`name`, '.retailcatalog.us/products/', c.`product_id`, '/', a.`image` ) AS image_url FROM `product_images` AS a LEFT JOIN `products` AS c ON (a.`product_id` = c.`product_id`) LEFT JOIN `industries` AS b ON (c.`industry_id` = b.`industry_id`) WHERE a.`product_id` = " . (int)$product_id . " ORDER BY a.`sequence` ASC LIMIT 10" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get product image urls.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Creates a new Craigslist ad
	 *
	 * @param int $craigslist_template_id
	 * @param int $product_id
	 * @param int $website_id
	 * @param int $duration
	 * @param string $title
	 * @param string $description
	 * @param int $active
	 * @param bool $publish
	 * @return int craigslist_ad_id
	 */
	public function create( $craigslist_template_id, $product_id, $website_id, $duration, $title, $text, $active, $publish ){ 
		// echo $craigslist_template_id, ' : ', $product_id, ' : ', $website_id, ' : ', $duration, ' : ', $title, ' : ', $text, ' : ', $active, ' : ', $publish, ' : '; exit;
		$date = ( $publish ) ? date( "Y-m=d H:i:s", time() ) : "0";
		$result = $this->db->insert( 'craigslist_ads', 
						  array( 'craigslist_template_id' => $craigslist_template_id, 
								 'product_id' => $product_id,
								 'website_id' => $website_id,
								 'duration' => $duration,
								 'title' => $title,
								 'text' => $text,
								 'active' => $active,
								 'date_created' => date( "Y-m=d H:i:s", time() ),
								 'date_posted' => $date ),
						  'iiiississ' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return $result;
	}
	
	/**
	 * Deletes a craigslist ad from the database
	 *
	 * @param int $craigslist_ad_id
	 * @return bool
	 */
	public function delete( $craigslist_ad_id ) {			
		$this->db->update( 'craigslist_ads', array( 'active' => '0' ), array( 'craigslist_ad_id' => $craigslist_ad_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return true;
	}
	
	/**
	 * Clones a craigslist ad from the database
	 *
	 * @since 1.0.0
	 *
	 * @var int $craigslist_ad_id
	 * @return bool false if couldn't delete
	 */
	public function copy( $craigslist_ad_id ) {
		$ad = $this->db->prepare( "SELECT `craigslist_template_id`, `product_id`, `website_id`, `title`, `text`, `craigslist_city_id`, `craigslist_category_id`, `craigslist_district_id` FROM `craigslist_ads` WHERE `craigslist_ad_id` = ?", 'i', $craigslist_ad_id )->get_row('', ARRAY_A);
		$this->db->insert( 'craigslist_ads', array( 'craigslist_template_id' => $ad['craigslist_template_id'], 'product_id' => $ad['product_id'], 'website_id' => $ad['website_id'], 'title' => $ad['title'], 'text' => $ad['text'], 'craigslist_city_id' => $ad['craigslist_city_id'], 'craigslist_category_id' => $ad['craigslist_category_id'], 'craigslist_district_id' => $ad['craigslist_district_id'], 'date_created' => date( "Y-m=d H:i:s", time() ) ), 'iiissiiis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to copy Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return true;
	}
	
	/**
	 * Updates an existing Craigslist ad
	 *
	 * @param int $craigslist_ad_id
	 * @param int $craigslist_template_id
	 * @param int $product_id
	 * @param int $website_id
	 * @param int $duration
	 * @param string $title
	 * @param string $description
	 * @param int $active
	 * @param bool $publish
	 * @return int craigslist_ad_id
	 */
	public function update( $craigslist_ad_id, $craigslist_template_id, $product_id, $website_id, $duration, $title, $text, $active, $publish ){		
		$date = ( $publish ) ? date( "Y-m=d H:i:s", time() ) : "0";
		$result = $this->db->update( 'craigslist_ads', 
						  array( 'craigslist_template_id' => $craigslist_template_id, 
								 'product_id' => $product_id,
								 'website_id' => $website_id,
								 'duration' => $duration,
								 'title' => $title,
								 'text' => $text,
								 'active' => $active,
								 'date_updated' => date( "Y-m=d H:i:s", time() ),
								 'date_posted' => $date ),
			array( 'craigslist_ad_id' => $craigslist_ad_id ), 'iiiississ', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return $result;
	}
	
	/**
	 * Gets misc website info
	 *
	 * @param int $websites_id
	 * @return array
	 */
	public function get_website_info( $website_id ){
		$results = $this->db->prepare( "SELECT `title` AS `website_name`, `domain`, `logo` FROM `websites` WHERE `website_id` = ?", 'i', $website_id )->get_row('', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website info.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}	
}