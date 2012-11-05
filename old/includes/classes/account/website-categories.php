<?php
/**
 * Handles all the Website Categories
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Website_Categories extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get List
	 *
	 * Recursively gets a list of categories
	 *
	 * @returns string $str_categories all the categories in a drop down format
	 */
	public function get_list() {
		return $this->generate_list( $this->get_ids() );
	}
	
	/**
	 * Get List
	 *
	 * Recursively gets a list of categories
	 *
	 * @params int $category_id (Optional)
	 * @params int $parent_category (Optional)
	 * @params int $spacing (Optional) number of spaces to put before a category
	 * @returns string $str_categories all the categories in a drop down format
	 */
	public function generate_list( $website_categories, $parent_category = 0, $spacing = 0 ) {
		$str_categories = '';
		
		// Type Juggling
		$parent_category = (int) $parent_category;
		
		// @Fix should use something like the category model -- not recursive queries
		$category = $this->db->get_results( "SELECT `category_id`, `name` FROM `categories` WHERE `parent_category_id` = $parent_category", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get category.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( is_array( $category ) )
		foreach ( $category as $cat ) {
			// Make sure its in the website categories list
			if ( !in_array( $cat['category_id'], $website_categories ) )
				continue;
			
        	$str_categories .= '<option value="' . $cat['category_id'] . '">' . str_repeat( '&nbsp;', $spacing ) . $cat['name'] . '</option>';
			$str_categories .= $this->generate_list( $website_categories, $cat['category_id'], ( $spacing + 5 ) );
		}
		
		return $str_categories;
	}
	
	/**
	 * Get website category ids
	 *
	 * @return array
	 */
	public function get_ids() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$website_categories = $this->db->get_col( "SELECT `category_id` FROM `website_categories` WHERE `website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get category ids.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_categories;
	}

    /**
     * Block categories
     *
     * @param array $category_ids
     * @return bool
     */
    public function block_categories( array $category_ids ) {
        if ( empty( $category_ids ) )
            return true;

        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        foreach ( $category_ids as &$cid ) {
            $cid = (int) $cid;
        }

        $values = "( $website_id, " . implode( " ), ( $website_id, ", $category_ids ) . ' )';

        // Insert into blocked list
        $this->db->query( "INSERT INTO `website_blocked_category` ( `website_id`, `category_id` ) VALUES $values" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to block category ids.', __LINE__, __METHOD__ );
			return false;
		}

        // Remove these
        $category_ids = implode( ',', $category_ids );

        // Remove the categories
        $this->db->query( "DELETE FROM `website_categories` WHERE `website_id` = $website_id AND `category_id` IN ( $category_ids )" );

        if ( $this->db->errno() ) {
			$this->_err( 'Failed to remove blocked categories.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
     * Unblock categories
     *
     * @param array $category_ids
     * @return bool
     */
    public function unblock_categories( array $category_ids ) {
        if ( empty( $category_ids ) )
            return true;

        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        foreach ( $category_ids as &$cid ) {
            $cid = (int) $cid;
        }

        $category_ids = implode( ',', $category_ids );

        $this->db->query( "DELETE FROM `website_blocked_category` WHERE `website_id` = $website_id AND `category_id` IN ( $category_ids )" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to unblock category ids.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
	 * Get blocked website category ids
	 *
	 * @return array
	 */
	public function get_blocked_categories() {
		global $user;

		// Type Juggling
		$website_id = (int) $user['website']['website_id'];

		$website_blocked_categories = $this->db->get_col( "SELECT `category_id` FROM `website_blocked_category` WHERE `website_id` = $website_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get blocked category ids.', __LINE__, __METHOD__ );
			return false;
		}

		return $website_blocked_categories;
	}
	
	 /**
	  * Get all sub categories related to a website and a main category
	  *
	  * @param int $category_id
	  * @return array
	  */
	 public function get_all_child_categories( $category_id ) {
		 // Instantiate Class
 		 $c = new Categories;
			
		// Define variables
		$child_categories = array();
		 
		// Get IDs
		$website_category_ids = $this->get_ids();
		 
		 // Get children
		$all_child_categories = $c->get_child_categories( $category_id );
		 
		if ( is_array( $all_child_categories ) )
		foreach ( $all_child_categories as $cc ) {
			if ( !in_array( $cc['category_id'], $website_category_ids ) )
				continue;
			
			$child_categories[] = $cc['category_id'];

			// Get sub sub categories
			$child_categories = array_merge( $child_categories, $this->get_all_child_categories( $cc['category_id'] ) );
		}
		 
		return $child_categories;
	}
	
	/**
	 * Sets a category image
	 *
	 * @param int $category_id
	 * @param string $image_url
	 * @return bool
	 */
	public function set_category_image( $category_id, $image_url ) {
		global $user;
		
		// Type Juggling & Protecting
		$category_id = (int) $category_id;
		$image_url = $this->db->escape( $this->_small_image( $image_url ) );
		$website_id = (int) $user['website']['website_id'];
		
		$this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES ( $website_id, $category_id, '$image_url' ) ON DUPLICATE KEY UPDATE `image_url` = '$image_url'" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to set category image.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
    
    /**
     * Small Image
     *
     * Turns a product image to its small version (200x200)
     *
     * @param string $image
     * @return string
     */
    private function _small_image( $image ) {
        return preg_replace( '/(.+\/products\/[0-9]+\/)(?:small\/)?([a-zA-Z0-9-.]+)/', "$1small/$2", $image );
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