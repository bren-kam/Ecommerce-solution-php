<?php
/**
 * Handles all categories
 *
 * @package Grey Suit Retail
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
	 *
	 * @param bool $restrict_categories [optional]
	 */
	public function __construct( $restrict_categories = true ) {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		// Load categories
		$this->load_categories( $restrict_categories );
	}
	
	/**
	 * Load All the category variables for this website (indicated by their industry)
     *
	 * @param bool $restrict_categories [optional]
     * @return array
	 */
	public function load_categories( $restrict_categories = true ) {
        // Get every category in the system
		$categories = $this->db->get_results( 'SELECT `category_id`, `parent_category_id`, `name`, `slug` FROM `categories` ORDER BY `parent_category_id` ASC, `sequence` ASC, `name` ASC', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( $restrict_categories ) {
			global $user;
	
			// Get valid categories for this website
			$valid_categories = $this->db->prepare( "SELECT DISTINCT c.`category_id`, c.`parent_category_id` FROM `categories` AS c LEFT JOIN `product_categories` AS pc ON ( c.`category_id` = pc.`category_id` ) LEFT JOIN `products` AS p ON ( pc.`product_id` = p.`product_id` ) LEFT JOIN `website_industries` AS wi ON ( p.`industry_id` = wi.`industry_id` ) WHERE ( p.`website_id` = 0 OR p.`website_id` = ? ) AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00' AND wi.`website_id` = ?", 'ii', $user['website']['website_id'], $user['website']['website_id'] )->get_results('');
	
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get valid categories.', __LINE__, __METHOD__ );
				return false;
			}
	
			// Narrow down to new set of categories
			$new_categories = $parent_categories = array();
			$categories = ar::assign_key( $categories, 'category_id' );
	
			foreach ( $valid_categories as $vc ) {
				$parent_category_id = $vc['category_id'];
				$last_parent_category_id = -1;
	
				while ( $parent_category_id != 0 && $parent_category_id != $last_parent_category_id ) {
					$category = $categories[$parent_category_id];
					$new_categories[$category['category_id']] = $category;
					$parent_category_id = $category['parent_category_id'];
				}
			}
		} else {
			$new_categories = $categories;
		}

        // Go through new categories to get them as we must
		foreach ( $new_categories as $c ) {
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
     * Get Category
     *
     * @param int $category_id
     * @return array
     */
    public function get( $category_id ) {
        return $this->categories_list[$category_id];
    }

	/**
	 * Get List
	 *
	 * Recursively gets a list of categories
	 *
	 * @param int $category_id (optional|0)
	 * @param int $parent_category (optional|0)
	 * @param int $spacing (optional|0) number of spaces to put before a category
     * @param bool $bottom_level_categories Whether bottom level categories should be included
	 * @return string $str_categories
	 */
	public function get_list( $category_id = 0, $parent_category = 0, $spacing = 0, $bottom_level_categories = true ) {
		$str_categories = '';
		
		foreach ( $this->categories[$parent_category] as $c ) {
            // If we don't have bottom level categories, skip them
            if ( !$bottom_level_categories && ( !isset( $this->categories[$c['category_id']] ) || !is_array( $this->categories[$c['category_id']] ) ) )
                continue;

			$selected = ( $category_id == $c['category_id'] ) ? ' selected="selected"' : '';
			$str_categories .= '<option value="' . $c['category_id'] . '"' . $selected . '>' . str_repeat( '&nbsp;', $spacing ) . $c['name'] . '</option>';
			
			if ( isset( $this->categories[$c['category_id']] ) && is_array( $this->categories[$c['category_id']] ) )
				$str_categories .= $this->get_list( $category_id, $c['category_id'], $spacing + 5, $bottom_level_categories );
			
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
	 * Get Top Category
	 *
	 * @param int $category_id
	 * @return array
	 */
	public function get_top( $category_id ) {
		if ( 0 == $category_id )
			return false;

        return ( 0 == $this->categories_list[$category_id]['parent_category_id'] ) ? $this->categories_list[$category_id] : $this->get_top( $this->categories_list[$category_id]['parent_category_id'] );
	}
	
	/**
	 * Get Parent Category IDs
	 *
	 * @param int $category_id
	 * @param array $parent_category_ids (optional)
	 * @return array
	 */
	public function get_parent_category_ids( $category_id, $parent_category_ids = array() ) {
		$category = ( isset( $this->categories_list[$category_id] ) ) ? $this->categories_list[$category_id] : NULL;

		// If they went too far, return what we have
		if ( is_null( $category ) )
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
	 * @return array|bool
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
	 * List Categories
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_categories( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$categories = $this->db->get_results( "SELECT a.`category_id`, IF ( '' = a.`title`, b.`name`, a.`title` ) AS title, UNIX_TIMESTAMP( a.`date_updated` ) AS date_updated, b.`slug` FROM `website_categories` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
        
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list categories.', __LINE__, __METHOD__ );
			return false;
		}

		return $categories;
	}

	/**
	 * Count Categories
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_categories( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( a.`category_id` ) FROM `website_categories` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) WHERE 1 $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count categories.', __LINE__, __METHOD__ );
			return false;
		}

		return $count;
	}

    /**
     * Get Website Category
     *
     * @param int $category_id
     * @return array
     */
    public function get_website_category( $category_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $category_id = (int) $category_id;

        $category = $this->db->get_row( "SELECT IF( '' = a.`title`, b.`name`, a.`title` ) AS title, IF( '' = a.`slug`, b.`slug`, a.`slug` ) AS slug, a.`content`, a.`meta_title`, a.`meta_description`, a.`meta_keywords`, a.`top` FROM `website_categories` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) WHERE a.`website_id` = $website_id AND a.`category_id` = $category_id", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website category.', __LINE__, __METHOD__ );
			return false;
		}

        return $category;
    }

    /**
     * Update Website Category
     *
     * @param int $category_id
     * @param string $title
     * @param string $slug
     * @param string $content
     * @param string $meta_title
     * @param string $meta_description
     * @param string $meta_keywords
     * @param bool $top
     * @return array
     */
    public function update_website_category( $category_id, $title, $slug, $content, $meta_title, $meta_description, $meta_keywords, $top ) {
        global $user;

        $this->db->update( 'website_categories', array( 'title' => $title, 'content' => $content, 'meta_title' => $meta_title, 'meta_description' => $meta_description, 'meta_keywords' => $meta_keywords, 'top' => $top ), array( 'website_id' => $user['website']['website_id'], 'category_id' => $category_id ), 'sssssi', 'ii' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update website category.', __LINE__, __METHOD__ );
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