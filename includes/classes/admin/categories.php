<?php
/**
 * Handles all the Categories
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Categories extends Base_Class {
	/**
	 * Hold all the categories
	 *
	 * @since 1.0.0
	 * @var array
	 * @access public
	 */
	private $categories_list = array();
	
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		$this->load_categories();
	}
	
	/**
	 * Create Category
	 *
	 * @param int $parent_category_id ( 0 if empty )
	 * @param string $name
	 * @param string $slug the slug for the URL (i.e. 'dining-room')
	 * @param string $attributes CSV to be added as attributes
	 * @return bool
	 */
	public function create( $parent_category_id, $name, $slug, $attributes ) {
		$this->db->insert( 'categories', array( 'parent_category_id' => $parent_category_id, 'name' => $name, 'slug' => format::slug( $slug ), 'sequence' => 1000 ), 'issi' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create category.', __LINE__, __METHOD__ );
			return false;
		}
		
		$category_id = $this->db->insert_id;
		
		$this->update_existing_sequence( $parent_category_id );
		
		//Add Attributes
		$attributes_array = explode( '|', $attributes );
		if ( is_array( $attributes_array ) ) {
			$a = new Attributes;
			$a->add_relations( $attributes_array, (int) $category_id );
		}

		return $category_id;
	}
	
	/**
	 * Update Category
	 *
	 * @param int $category_id (the one to change)
	 * @param int $parent_category_id ( 0 if empty )
	 * @param string $name
	 * @param string $slug the slug for the URL (i.e. 'dining-room')
	 * @param string $attributes CSV to be added as attributes
	 * @return bool
	 */
	public function update( $category_id, $parent_category_id, $name, $slug, $attributes ) {
		$this->db->update( 'categories', array( 'parent_category_id' => $parent_category_id, 'name' => $name, 'slug' => format::slug( $slug ) ), array( 'category_id' => $category_id ), 'iss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update category.', __LINE__, __METHOD__ );
			return false;
		}
		
		//Add Attributes
		$attributes_array = explode( '|', $attributes );
		if ( is_array( $attributes_array ) ) {
			$category_id = (int) $category_id;
			
			$a = new Attributes;
			$a->delete_relations( $category_id );
			$a->add_relations( $attributes_array, $category_id );
		}

		return true;
	}
	
	/**
	 * Update Category sequences
	 *
	 * @param $parent_category_id
	 * @param $sequence  is the new sequence to be assigned
	 * @return nothing
	 */
	 public function update_sequence( $parent_category_id, $categories ) {
		// Starting with 0 for a sequence
		$sequence = 0;
		
		// Prepare statement
		$statement = $this->db->prepare( 'UPDATE `categories` SET `sequence` = ? WHERE `parent_category_id` = ' . $parent_category_id . ' AND `category_id` = ?' );
		$statement->bind_param( 'ss', $sequence, $category_id );
		
		// Loop through the statement and update anything as it needs to be updated
		foreach ( $categories as $category_id ) {
			$statement->execute();
			
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( "Failed to update category's existing sequence.", __LINE__, __METHOD__ );
				return false;
			}
			
			$sequence++;
		}
		
		return true;
	 }
	 
	/**
	 * Update Sequence with given category id
	 *
	 * @param $parent_category_id
	 * @return bool
	 */
	public function update_existing_sequence( $parent_category_id ) {
		$categories = $this->get_child_categories( $parent_category_id );
		$sequence = 0;
		
		// Prepare statement
		$statement = $this->db->prepare( 'UPDATE `categories` SET `sequence` = ? WHERE `parent_category_id` = ' . $parent_category_id . ' AND `category_id` = ?' );
		$statement->bind_param( 'ss', $sequence, $category_id );
		
		// Loop through the statement and update anything as it needs to be updated
		foreach ( $categories as $c ) {
			$category_id = $c['category_id'];
			
			$statement->execute();
			
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( "Failed to update category's existing sequence.", __LINE__, __METHOD__ );
				return false;
			}
			
			$sequence++;
		}
		
		return true;
	}
	
	/**
	 * Load Category Data
	 */
	public function load_categories() {
		// @Fix -- the database does not need page_title, met_description and meta_keywords	
		$categories = $this->db->get_results( "SELECT `category_id`, `parent_category_id`, `name`, `slug` FROM `categories` ORDER BY `parent_category_id` ASC, sequence ASC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to load categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		foreach ( $categories as $c ) {
			// Assign the categories list in a way for infinite nesting
			$this->categories[$c['parent_category_id']][] = $c;
	
			// The categories list in normal order
			$this->categories_list[$c['category_id']] = $c;
	
			// The categories by slug
			$this->categories_slug[$c['slug']] = array( $c['category_id'], $c['parent_category_id'] );
		}
	}
	
	/**
	 * Get Child Category
	 *
	 * @param int $category_id (optional|0)
	 * @return array
	 */
	public function get_child_categories( $category_id = 0 ) {
		$categories = array();
		
		foreach ( $this->categories_list as $i => $c ) {
			if ( $category_id == $c['parent_category_id'] ) 
				$categories[] = $c;
		}
		
		return ( 0 == count( $categories ) ) ? false : $categories;
	}
	
	/**
	 * Get Category
	 *
	 * @param $category_id the fields
	 * @return array
	 */
	public function get_category( $category_id ) {
		return ( isset( $this->categories_list[$category_id] ) ) ? $this->categories_list[$category_id] : NULL;
	}
	
	/**
	 * Get Category Chain
	 *
	 * @param $id the fields
	 * @return array
	 */
	 public function get_chain( $category_id ) {
		$result_array = array();
		
		while( 0 != $category_id ) {
			$c = $this->get_category( $category_id );
			$result_array[] = array( 'category_name' => $c['name'], 'category_id' => $c['category_id'] , 'slug' => $c['slug'] );
			
			$category_id = $c['parent_category_id'];
		}
		
		$result_array[] = array( 'category_name' => 'Main Categories', 'category_id' => 0 );
				
		return $result_array;
	}
	 
	/**
	 * Get List
	 *
	 * Recursively gets a list of categories
	 *
	 * @param int $category_id (optional|0)
	 * @param int $parent_category (optional|0)
	 * @param int $spacing (optional|0) number of spaces to put before a category
	 * @param int $skip_id (optional|0) the category_id you want to skip
	 * @return string $str_categories all the categories in a drop down format
	 */
	public function get_list( $category_id = 0, $parent_category = 0, $spacing = 0, $skip_id = 0 ) {
		$str_categories = '';
		$categories = $this->db->get_results( "SELECT `category_id`, `parent_category_id`, `name`, `slug` FROM `categories` WHERE `parent_category_id` = $parent_category", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get categories list.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( is_array( $categories ) )
		foreach ( $categories as $c ) {
			// Don't want to get that category
			if ( $c['category_id'] == $skip_id )
				continue;
			
			$selected = ( $category_id == $c['category_id'] ) ? ' selected="selected"' : '';
			
			$str_categories .= '<option value="' . $c['category_id'] . '"' . $selected . '>' . str_repeat( '&nbsp;', $spacing ) . $c['name'] . '</option>';
			$str_categories .= $this->get_list( $category_id, $c['category_id'], ( $spacing + 5 ), $skip_id );
		}
		
		return $str_categories;
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
	 * Deletes a category and categories under it
	 *
	 * @param int $category_id
	 * @return bool
	 */
	public function delete( $category_id ) {
		$category_id = (int) $category_id;
		
		if ( 0 == $category_id )
			return false;
		
		$this->db->query( "DELETE FROM `categories` WHERE `category_id` = $category_id OR `parent_category_id` = $category_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
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