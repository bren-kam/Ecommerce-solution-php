<?php
/**
 * Handles all the Product Options
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Product_Options extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Creates a product option and puts it into the database
	 *
	 * @param string $option_type the type of option (select/checkbox/text/textarea)
	 * @param string $option_title The title as the customer sees it
	 * @param string $option_name The name of the option, for internal use
	 * @param array $list_items (optional) the list items for a select box
	 * @return int
	 */
	public function create( $option_type, $option_title, $option_name, $list_items = array() ) {
		$this->db->insert( 'product_options', array( 'option_type' => $option_type, 'option_title' => $option_title, 'option_name' => $option_name ), 'sss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create product option.', __LINE__, __METHOD__ );
			return false;
		}
		
		$product_option_id = $this->db->insert_id;
		
		$i = 0;
		
		if ( 'select' == $option_type && is_array( $list_items ) ) {
			$values = '';
			
			foreach ( $list_items as $li ) {
				if ( !empty( $values ) )
					$values .= ',';
				
				// Get the other information
				list( $product_option_list_item_id, $li ) = explode ( ':', $li );
				
				
				$values .= "( $product_option_id, '" . $this->db->escape( $li ) . "', $i )";
				$i++;
			}
			
			$this->db->query( "INSERT INTO `product_option_list_items` ( `product_option_id`, `value`, `sequence` ) VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to create product option list items.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return $product_option_id;
	}
	
	/**
	 * Updates a product option and puts it into the database
	 *
	 * @param string $option_type the type of option (select/checkbox/text/textarea)
	 * @param string $option_title The title as the customer sees it
	 * @param string $option_name The name of the option, for internal use
	 * @param array $list_items (optional) the list items for a select box
	 * @param int $product_option_id
	 * @return bool
	 */
	public function update( $option_type, $option_title, $option_name, $list_items, $product_option_id ) {
		// Typecast
		$product_option_id = (int) $product_option_id;
		
		$this->db->update( 'product_options', array( 'option_type' => $option_type, 'option_title' => $option_title, 'option_name' => $option_name ), array( 'product_option_id' => $product_option_id ), 'sss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update product option.', __LINE__, __METHOD__ );
			return false;
		}
		
		$product_option = $this->db->insert_id;
		
		$i = 0;
		
		if ( 'select' == $option_type ) {
			$i = 0;
			$list_item_ids = '';
			
			foreach ( $list_items as $li ) {
				list( $product_option_list_item_id, $li ) = explode ( ':', $li );
				
				if ( !empty( $list_item_ids ) )
					$list_item_ids .= ',';
				
				if ( 0 == $product_option_list_item_id ) {
					$this->db->insert( 'product_option_list_items', array( 'product_option_id' => $product_option_id, 'value' => $li, 'sequence' => $i ), 'isi' );
				
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to create product option list item.', __LINE__, __METHOD__ );
						return false;
					}
					
					$list_item_ids .= $this->db->insert_id;
				} else {
					$this->db->update( 'product_option_list_items', array( 'value' => $li, 'sequence' => $i ), array ( 'product_option_list_item_id' => $product_option_list_item_id ), 'si', 'i' );
					
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to update product option list item.', __LINE__, __METHOD__ );
						return false;
					}
					
					$list_item_ids .= (int) $product_option_list_item_id;
				}
				
				$i++;
			}
			
			// Delete any that were not in the list_item_ids
			$this->db->query( "DELETE FROM `product_option_list_items` WHERE `product_option_id` = $product_option_id AND `product_option_list_item_id` NOT IN ($list_item_ids)" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to delete product option list items.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Get all information of the product options
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_product_options( $where, $order_by, $limit ) {
		// Get the users
		$product_options = $this->db->get_results( "SELECT `product_option_id`, `option_title`, `option_name`, `option_type` FROM `product_options` WHERE 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list product options.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_options;
	}
	
	/**
	 * Count all the product options
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_product_options( $where ) {
		// Get the attribute count
		$product_option_count = $this->db->get_var( "SELECT COUNT( `product_option_id` ) FROM `product_options` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count product options.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_option_count;
	}
	
	/**
	 * Gets a specific product option
	 *
	 * @param int $product_option_id
	 * @return array
	 */
	public function get( $product_option_id ) {
		// Typecast
		$product_option_id = (int) $product_option_id;
		
		// Get product option
		$product_option = $this->db->get_row( "SELECT * FROM `product_options` WHERE `product_option_id` = $product_option_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product option.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( 'select' == $product_option['option_type'] ) {
			$options = $this->db->get_results( "SELECT `product_option_list_item_id`, `value` FROM `product_option_list_items` WHERE `product_option_id` = $product_option_id ORDER BY `sequence`", ARRAY_A );

			if ( is_array( $options ) )
			foreach ( $options as $o ) {
				$product_option['extra'][$o['product_option_list_item_id']] = $o['value'];
			}
		}

		return $product_option;
	}
	
	/**
	 * Get All
	 *
	 * @param int $brand_id
	 * @return array
	 */
	public function get_all() {
		$product_options = $this->db->get_results( 'SELECT * FROM `product_options`', ARRAY_A );
		
		if ( mysql_errno() ) {
			$this->err( 'Failed to get product options', __LINE__, __METHOD__ );
			return false;
		}
		
		return ar::assign_key( $product_options, 'product_option_id' );
	}
	
	/**
	 * Delete by brand
	 *
	 * @param int $brand_id
	 * @return bool
	 */
	public function delete_by_brand( $brand_id ) {
		// Delete product options by brand
		$this->db->query( 'DELETE FROM `product_option_relations` WHERE `brand_id` = ' . (int) $brand_id );
		
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete product options', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete product option
	 *
	 * @param int $product_option_id
	 * @return bool
	 */
	public function delete( $product_option_id ) {
		// Typecast
		$product_option_id = (int) $product_option_id;
		
		// Delete product option
		$this->db->query( "DELETE FROM `product_options` WHERE `product_option_id` = $product_option_id" );
		
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete product option', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete the list items belonging to that product option
		$this->db->query( "DELETE FROM `product_option_list_items` WHERE `product_option_id` = $product_option_id" );
		
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete product option list items', __LINE__, __METHOD__ );
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