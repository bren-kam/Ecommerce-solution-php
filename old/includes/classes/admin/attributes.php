<?php
/**
 * Handles all the Attributes
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Attributes extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Creates an attribute and puts it into the database
	 *
	 * @param string $title
	 * @param string $name
	 * @param array $attribute_items
	 * @return int
	 */
	public function create( $title, $name, $attribute_items ) {
		$this->db->insert( 'attributes', array( 'title' => $title, 'name' => $name ), 'ss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create attribute.', __LINE__, __METHOD__ );
			return false;
		}
		
		$attribute_id = $this->db->insert_id;
		
		// Now add the attribute items
		$sequence = 0;
		$values = '';
		
		if ( is_array( $attribute_items ) )
		foreach ( $attribute_items as $ai ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $attribute_id, '" . $this->db->escape( $ai ) . "', $sequence )";
			$sequence++;
		}

		$this->db->query( "INSERT INTO `attribute_items` ( `attribute_id`, `attribute_item_name`, `sequence` ) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create attribute items.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $attribute_id;
	}
	
	/**
	 * Returns all the attribute items for a product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_attribute_items_by_product( $product_id ) {
		$attribute_items = $this->db->get_results( 'SELECT a.`attribute_item_id`, a.`attribute_id`, a.`attribute_item_name`, c.`title` FROM `attribute_items` AS a LEFT JOIN `attribute_item_relations` AS b ON (a.`attribute_item_id` = b.`attribute_item_id`) INNER JOIN `attributes` AS c ON (a.`attribute_id` = c.`attribute_id`) WHERE b.`product_id` = ' . (int) $product_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attribute items by product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $attribute_items;
	}
	
	/**
	 * Adds a relation between an attribute item and a product
	 *
	 * @param string $attribute_items an array of | separated attribute item ids
	 * @param int $product_id
	 * @return bool
	 */
	public function add_attribute_item_relations( $attribute_items, $product_id ) {
		// $attribute_item_array = explode( '|', substr( $attribute_items, 0, -1 ) );
		$attribute_item_array = explode( '|', $attribute_items  );
		
		if ( !is_array( $attribute_item_array ) )
			return true;
		
		$values = '';
		
		foreach ( $attribute_item_array as $attribute_item_id ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= sprintf( "( %d, $product_id )", $attribute_item_id );
		}
		
		// Insert attribute item relations
		$this->db->query( "INSERT INTO `attribute_item_relations` (`attribute_item_id`, `product_id`) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add attribute item relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets a single attribute
	 *
	 * @param int $attribute_id
	 * @return array
	 */
	public function get( $attribute_id ) {
		$attribute_id = (int) $attribute_id;
		
		$attribute = $this->db->get_row( "SELECT * FROM `attributes` WHERE `attribute_id` = $attribute_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attribute.', __LINE__, __METHOD__ );
			return false;
		}
		
		$attribute_items = $this->db->get_results( "SELECT `attribute_item_id`, `attribute_item_name` FROM `attribute_items` WHERE `attribute_id` = $attribute_id ORDER BY `sequence`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attribute items.', __LINE__, __METHOD__ );
			return false;
		}
		
		return array( $attribute, $attribute_items );
	}
	
	/**
	 * Updates an attribute
	 *
	 * @param int $attribute_id
	 * @param string $title
	 * @param string $name
	 * @param array $attribute_items
	 * @return int
	 */
	public function update( $attribute_id, $title, $name, $attribute_items ) {
		$this->db->update( 'attributes', array( 'title' => $title, 'name' => $name ), array( 'attribute_id' => $attribute_id ), 'ss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update attribute.', __LINE__, __METHOD__ );
			return false;
		}
		
		$sequence = 0;
		
		if ( is_array( $attribute_items ) )
		foreach ( $attribute_items as $ai ) {
			// Get attribute info
			list( $ai_name, $ai_id ) = explode( '|', $ai );
			
			if ( !empty( $ai_id ) ) {
				// Comprise a list of the IDs not to delete
				$ai_id_list[] = $ai_id;
				
				$this->db->update( 'attribute_items', array( 'attribute_item_name' => $ai_name, 'sequence' => $sequence ), array( 'attribute_item_id' => $ai_id ), 'si', 'i' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to update attribute item.', __LINE__, __METHOD__ );
					return false;
				}
			} else {
				$this->db->insert( 'attribute_items', array( 'attribute_id' => $attribute_id, 'attribute_item_name' => $ai_name, 'sequence' => $sequence ), 'isi' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert attribute item.', __LINE__, __METHOD__ );
					return false;
				}
				
				$ai_id_list[] = $this->db->insert_id;
			}
			
			$sequence++;
		}
		
		// Delete any items in the list that are no longer there
		$this->db->query( 'DELETE FROM `attribute_items` WHERE `attribute_id` = ' . (int) $attribute_id . ' AND `attribute_item_id` NOT IN (' . implode( ',', $ai_id_list ) . ")" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete attribute items.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets all the attributes
	 *
	 * @return array
	 */
	public function get_attributes() {
		$attributes = $this->db->get_results( 'SELECT * FROM `attributes` ORDER BY `title`', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attributes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ar::assign_key( $attributes, 'attribute_id' );
	}
	
	/**
	 * Deletes an attribute from the database
	 *
	 * @param int $attribute_id
	 * @return bool
	 */
	public function delete( $attribute_id ) {
		$attribute_id = (int) $attribute_id;
		
		$this->db->query( "DELETE FROM `attributes` WHERE `attribute_id` = $attribute_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete attribute.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->delete_attribute_items( $attribute_id );
	}
	
	/**
	 * Deletes the attribute item/product relations for a particular product
	 *
	 * @param int $product_id The product_id
	 * @return bool
	 */
	public function delete_item_relations( $product_id ) {
		$this->db->query( 'DELETE FROM `attribute_item_relations` WHERE `product_id` = ' . (int) $product_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete attribute item relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Deletes the attribute items from the database
	 *
	 * @param int $attribute_id
	 * @return bool
	 */
	public function delete_attribute_items( $attribute_id ) {
		$this->db->query( "DELETE FROM `attribute_items` WHERE `attribute_id` = $attribute_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete attribute items.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Deletes relations between an attributes and a category
	 *
	 * @since 1.0.0
	 *
	 * @param int $category_id
	 * @returns bool
	 */
	public function delete_relations( $category_id ) {
		$this->db->query( 'DELETE FROM `attribute_relations` WHERE `category_id` = ' . (int) $category_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete attribute relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Adds relations between an attributes and a category
	 *
	 * @since 1.0.0
	 *
	 * @param array $attribute_ids
	 * @param int $category_id
	 * @returns bool
	 */
	public function add_relations( $attribute_ids, $category_id ) {
		if ( !is_array( $attribute_ids ) )
			return false;
		
		$sql = '';
		
		foreach ( $attribute_ids as $attribute_id ) {
			if ( !empty( $sql ) )
				$sql .= ',';
			
			$sql = '(' . (int) $attribute_id . ', ' . $category_id . ')';
			
			$result = $this->db->query( "INSERT INTO `attribute_relations` ( `attribute_id`, `category_id` ) VALUES $sql" );
		}
		
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add attribute relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns all the attributes for a category
	 *
	 * @param int $category_id
	 * @return array
	 */
	public function get_category_attributes( $category_id ) {
		$category_attributes = $this->db->get_col( 'SELECT a.`attribute_id` FROM `attributes` AS a LEFT JOIN `attribute_relations` AS b ON ( a.`attribute_id` = b.`attribute_id` ) WHERE b.`category_id` = ' . (int) $category_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get category attributes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $category_attributes;
	}
	
	/**
	 * Gets all the attribute items
	 *
	 * @return array
	 */
	public function get_attribute_items() {
		$attribute_items = $this->db->get_results( "SELECT a.`attribute_item_id`, a.`attribute_item_name`, b.`title` FROM `attribute_items` AS a LEFT JOIN `attributes` AS b ON (a.`attribute_id` = b.`attribute_id`)", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attribute items.', __LINE__, __METHOD__ );
			return false;
		}
		
		$new_attribute_items = array();
		
		foreach ( $attribute_items as $ai ) {
			$new_attribute_items[$ai['title']][] = $ai;
		}
		
		return $new_attribute_items;
	}
	
	/**
	 * Gets all the attribute items based on categories
	 *
	 * @param string $categories CSV of category ids
	 * @return array
	 */
	public function get_attribute_items_by_categories( $categories ) {
		$attribute_items = $this->db->get_results( 'SELECT a.`attribute_item_id`, a.`attribute_item_name`, b.`title` FROM `attribute_items` AS a LEFT JOIN `attributes` AS b ON ( a.`attribute_id` = b.`attribute_id` ) LEFT JOIN `attribute_relations` AS c ON ( c.`attribute_id` = b.`attribute_id` ) WHERE c.`category_id` IN(' . preg_replace( '/[^0-9,]/', '', $categories ) . ')', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attribute items by categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		$new_attribute_items = array();
		
		foreach ( $attribute_items as $ai ) {
			$new_attribute_items[$ai['title']][] = $ai;
		}
		
		return $new_attribute_items;
	}
	
	/**
	 * Get all information of the attributes
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_attributes( $where, $order_by, $limit ) {
		// Get the users
		$attributes = $this->db->get_results( "SELECT `attribute_id`, `title` FROM `attributes` WHERE 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list attributes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $attributes;
	}
	
	/**
	 * Count all the attributes
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_attributes( $where ) {
		// Get the attribute count
		$attribute_count = $this->db->get_var( "SELECT COUNT( `attribute_id` ) FROM `attributes` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count attributes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $attribute_count;
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