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
	 * Adds a relation between an attribute item and a product
	 *
	 * @param string $attribute_items an array of | separated attribute item ids
	 * @param int $product_id
	 * @return bool
	 */
	public function add_attribute_item_relations( $attribute_items, $product_id ) {
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
	 * Gets all the attribute items based on categories
	 *
	 * @param string $categories CSV of category ids
	 * @return array
	 */
	public function get_attribute_items_by_categories( $categories ) {
		// That preg replace is brilliance!
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