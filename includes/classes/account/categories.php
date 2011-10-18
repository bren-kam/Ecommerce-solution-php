<?php
/**
 * Handles all categories
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Categories extends Base_Class {
	/**
	 * Categories list by parent category id
	 * @var array
	 */
	private $categories = array();
	
	/**
	 * Categories list by category_id
	 * @var array
	 */
	private $categories_list = array();

	/**
	 * Categories list by slug
	 * @var array
	 */
	private $categories_slug = array();

	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		// Load categories
		$this->load_categories();
	}
	
	/**
	 * Load All the category variables
	 */
	 public function load_categories() {
		$categories = $this->db->get_results( 'SELECT `category_id`, `parent_category_id`, `name`, `slug` FROM `categories` ORDER BY `parent_category_id` ASC, `sequence` ASC, `name` ASC', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		foreach ( $categories as $c ) {
			// Assign the categories list in a way for infinite nesting
			$parent_categories[$c['parent_category_id']][] = $c;
	
			// The categories list in normal order
			$this->categories_list[$c['category_id']] = $c;
	
			// The categories by slug
			$this->categories_slug[$c['slug']] = array( $c['category_id'], $c['parent_category_id'] );
		}

		$this->categories = $parent_categories;
	}
	
	/**
	 * Get List
	 *
	 * Recursively gets a list of categories
	 *
	 * @param int $category_id (optional|0)
	 * @param int $parent_category (optional|0)
	 * @param int $spacing (optional|0) number of spaces to put before a category
	 * @return string $str_categories
	 */
	public function get_list( $category_id = 0, $parent_category = 0, $spacing = 0 ) {
		$str_categories = '';
		
		foreach ( $this->categories[$parent_category] as $c ) {
			$selected = ( $category_id == $c['category_id'] ) ? ' selected="selected"' : '';
			$str_categories .= '<option value="' . $c['category_id'] . '"' . $selected . '>' . str_repeat( '&nbsp;', $spacing ) . $c['name'] . '</option>';
			
			if ( isset( $this->categories[$c['category_id']] ) && is_array( $this->categories[$c['category_id']] ) )
				$str_categories .= $this->get_list( $category_id, $c['category_id'], $spacing + 5 );
			
		}
		
		return $str_categories;
	}
	
	/**
	 * Gets the URL for a category
	 *
	 * @param int $category_id
	 * @return string
	 */
	public function category_url( $category_id ) {
		// Typecast
		$category_id = (int) $category_id;
		
		// Get the category
		$category = $this->get_category( $category_id );
		
		// If there is no category, return
		if ( !$category )
			return false;
		
		global $user;
		
		// Get the parent cateogires
		$parent_categories = $this->get_parent_categories( $category_id );
		
		// Initialize category URL
		$category_url = '';
		
		foreach ( $parent_categories as $pc ) {
			$category_url = $pc['slug'] . '/' . $category_url;
		}
		
		$subdomain = ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '';
		return 'http://' . $subdomain . $user['website']['domain'] . '/' . $category_url . $category['slug'] . '/';
	}
	
	/**
	 * Get Category
	 *
	 * @param $category_id
	 * @return array
	 */
	public function get_category( $category_id ) {
		if ( 0 == $category_id )
		 	return false;
		
		return $this->categories_list[$category_id];
	}
	
		/**
	 * Get Parent Categories
	 *
	 * @param int $category_id
	 * @param array $parent_categories (optional)
	 * @return array
	 */
	public function get_parent_categories( $category_id, $parent_categories = array() ) {
		if ( !$category_id )
			return false;
		
		// Get the categories
		$category = $this->categories_list[$category_id];
		
		// If they went too far, return what we have
		if ( empty( $category ) )
		 	return $parent_categories;
		
		// Find out if their is a parent
		if ( 0 != $category['parent_category_id'] ) {
			$parent_categories[] = $this->categories_list[$category['parent_category_id']];
		
			// If the parent has a parent, call this function again
			$parent_categories = $this->get_parent_categories( $category['parent_category_id'], $parent_categories );
		}
		
		return $parent_categories;
	}
	
	/**
	 * Get Parent Category IDs
	 *
	 * @param int $category_id
	 * @param array $parent_category_ids (optional)
	 * @returns array
	 */
	public function get_parent_category_ids( $category_id, $parent_category_ids = array() ) {
		$category = $this->categories_list[$category_id];
		 
		// If they went too far, return what we have
		if ( empty( $category ) )
		 	return $parent_category_ids;
		
		// Find out if there is a parent
		if ( 0 != $category['parent_category_id'] ) {
				$parent_category_ids[] = $category['parent_category_id'];
			
			// If the parent has a parent, call this function again
			if ( 0 != $this->categories_list[$category['parent_category_id']]['parent_category_id'] )
				$parent_category_ids = $this->get_parent_category_ids( $category['parent_category_id'], $parent_category_ids );
		}
		
		return $parent_category_ids;
	}
	
	/**
	 * Get Sub Category IDs
	 *
	 * @param int $category_id
	 * @param array $sub_category_ids (optional|array)
	 * @return array
	 */
	public function get_sub_category_ids( $category_id, $sub_category_ids = array() ) {
		// Check to see if it has any sub categories
		if ( array_key_exists( $category_id, $this->categories ) )
		foreach ( $this->categories[$category_id] as $cat ) {
			$sub_category_ids[] = $cat['category_id'];
			
			if ( array_key_exists( $cat['category_id'], $this->categories ) )
				$sub_category_ids = $this->get_sub_category_ids( $cat['category_id'], $sub_category_ids );
		}
		
		return $sub_category_ids;
	} 	
	
	/**
	 * Get Child Category
	 *
	 * @param int $id the fields
	 * @returns array|bool
	 */
	 public function get_child_categories( $id = 0 ) {
		 $cat = array ();
		 foreach ( $this->categories_list as $i => $c ) {
			 if ( $id == $c['parent_category_id'] ) 
			 	$cat[count( $cat )] = $c;
		 }
		 
		 return ( ( 0 == count ($cat) ) ? FALSE : $cat );
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