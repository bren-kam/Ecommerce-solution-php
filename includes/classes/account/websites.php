<?php
/**
 * Handles all the website information
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Websites extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get Website
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_website( $website_id ) {
		$website = $this->db->get_row( 'SELECT `website_id`, `os_user_id`, `user_id`, `domain`, `subdomain`, `title`, `theme`, `logo`, `phone`, `pages`, `products`, `product_catalog`, `link_brands`, `blog`, `email_marketing`, `shopping_cart`, `seo`, `room_planner`, `craigslist`, `social_media`, `domain_registration`, `additional_email_addresses`, `ga_profile_id`, `ga_tracking_key`, `wordpress_username`, `wordpress_password`, `mc_list_id`, `type`, `version`, `live`, `date_created`, `date_updated`  FROM `websites` WHERE `website_id` = ' . (int) $website_id, ARRAY_A );
	
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
	}
	
	/**
	 * Get website industries
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_website_industries( $industry_id ) {
		global $user;
		
		$industry_ids = $this->db->get_col( 'SELECT `industry_id` FROM `website_industries` WHERE `website_id` = ' . (int) $user['website']['website_id'] );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get industry ids.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industry_ids;
	}
	
	/**
	 * Update Website
	 *
	 * @param array $fields
	 * @param string $fields_safety
	 * @return bool
	 */
	public function update( $fields, $fields_safety ) {
		global $user;
		
		$this->db->update( 'websites', $fields, array( 'website_id' => $user['website']['website_id'] ), $fields_safety, 'i' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update website', __LINE__, __METHOD__ );
			return false;
		}
		
		// Update the user website
		$user['website'] = array_merge( $user['website'], $fields ); 
		
		return true;
	}
	
	/**
	 * Updates page information
	 *
	 * @param int $website_page_id
	 * @param string $content
	 * @param string $meta_title
	 * @param string $meta_description
	 * @param string $meta_keywords
	 * @return bool
	 */
	public function update_page( $website_page_id, $content, $meta_title, $meta_description, $meta_keywords ) {
		global $user;
		
		// Update existing request
		$this->db->update( 'website_pages', array( 'content' => stripslashes($content), 'meta_title' => $meta_title, 'meta_description' => $meta_description, 'meta_keywords' => $meta_keywords, 'updated_user_id' => $user['user_id'] ), array( 'website_page_id' => $website_page_id, 'website_id' => $user['website']['website_id'] ), 'ssssi', 'ii' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get check if request exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets a websites FTP data and unencrypts it
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_ftp_data( $website_id ) {
		$ftp_data = $this->db->get_row( 'SELECT `ftp_host`, `ftp_username`, `ftp_password` FROM `websites` WHERE `website_id` = ' . (int) $website_id, ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get FTP data.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $ftp_data;
	}
	
	/**
	 * Gets a specific page by the page_id
	 *
	 * @param int $website_page_id
	 * @return array
	 */
	public function get_page( $website_page_id ) {
		// Typecast
		$website_page_id = (int) $website_page_id;
		
		// Get the page
		$page = $this->db->get_row( "SELECT `website_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords` FROM `website_pages` WHERE `website_page_id` = $website_page_id", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get page.', __LINE__, __METHOD__ );
			return false;
		}
		
		// unencrypt data
		if( is_array( $page ) )
		foreach( $page as $k => $v ) {
			$new_page[$k] = html_entity_decode( $v, ENT_QUOTES, 'UTF-8' );
		}
		
		return $new_page;
	}
	
	/**
	 * Gets website pages
	 *
	 * @return array
	 */
	public function get_pages() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the page
		$pages = $this->db->get_results( "SELECT `slug`, `title` FROM `website_pages` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get pages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $pages;
	}
	
	/**
	 * Gets a metadata for a page
	 *
	 * @param int $website_id
	 * @param string $key_1, $key_2, $key_3, etc.
	 * @return array
	 */
	public function get_pagemeta_by_key() {
		// Get the arguments
		$arguments = func_get_args();
		
		// Needs to have at least two arguments
		if( count( $arguments ) <= 1 )
			return false;
		
		global $user;
		
		// Typecast
		$website_id = (int) $user['website']['website_id'];
		$website_page_id = (int) array_shift( $arguments );
		
		// Get keys, escape them and turn them into comma separated values
		array_walk( $arguments, array( $this->db, 'escape' ) );
		$keys = "'" . implode( "', '", $arguments ) . "'";
		
		// Get the meta data
		$metadata = $this->db->get_results( "SELECT `key`, `value` FROM `website_pagemeta` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) WHERE a.`key` IN ($keys) AND b.`website_page_id` = $website_page_id AND b.`website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get metadata.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Set the array
		$new_metadata = array_fill_keys( $arguments, '' );
		
		// Decrypt any meta data
		if( is_array( $metadata ) )
		foreach( $metadata as $md ) {
			$new_metadata[$md['key']] = html_entity_decode( $md['value'], ENT_QUOTES, 'UTF-8' );
		}
		
		return ( 1 == count( $new_metadata ) ) ? array_shift( $new_metadata ) : $new_metadata;
	}
	
	/**
	  * Sets the metadata for a page
	  *
	  * @Fix Need to remove website_pagemeta column
	  * 
	  * @param int $website_page_id
	  * @param array $metadata
	  * @return bool
	  */
	public function set_pagemeta( $website_page_id, $metadata ) {
		// Type Juggling
		$website_page_id = (int) $website_page_id;
		
		// Insert/update in one awesome query. Have to create the values for it first
		$values = '';
		
		foreach( $metadata as $k => $v ) {
			if( !empty( $values ) )
				$values .= ',';
			
			// Form values string
			$values .= "( $website_page_id, '" . $this->db->escape( $k ) . "', '" . $this->db->escape( $v ) . "' )";
		}
				
		// Insert the values, if they exist, update them instead
		$this->db->query( "INSERT INTO `website_pagemeta` ( `website_page_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update all the website pagemeta', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * List Pages
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_pages( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$pages = $this->db->get_results( "SELECT `website_page_id`, `slug`, `title`, `status`, UNIX_TIMESTAMP( `date_updated` ) AS date_updated FROM `website_pages` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to list pages.', __LINE__, __METHOD__ );
			return false;
		}
			
		return $pages;
	}
	
	/**
	 * List Authorized users
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_pages( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `website_page_id` ) FROM `website_pages` WHERE 1 $where" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to count pages.', __LINE__, __METHOD__ );
			return false;
		}
			
		return $count;
	}
	
	/**
	 * Gets page by the slug
	 *
	 * @param string $slug
	 * @return array
	 */
	public function get_page_by_slug( $slug ) {
		global $user;
		
		$page = $this->db->prepare( 'SELECT `website_page_id`, `slug`, `title`, `content` FROM `website_pages` WHERE `slug` = ? AND `website_id` = ? AND `status` = 1', 'si', $slug, $user['website']['website_id'] )->get_row( '', ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get page by slug.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $page;
	}
	
	/***** ATTACHMENTS *****/
	
	/**
	 * Get an attachment by name
	 * 
	 * @param int $website_page_id
	 * @param string $key
	 * @return array
	 */
	public function get_attachments_by_name( $website_page_id, $key ) {
		$attachments = $this->db->prepare( 'SELECT `website_attachment_id`, `key, `value` FROM `website_attachments` WHERE `key` = ? AND `website_page_id` = ?', 'si', $key, $website_page_id )->get_results( '', ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get attachments by name.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( 1 == count( $attachments ) ) ? $attachments[0] : $attachments;
	}
	
	/***** OTHER METHODS *****/
	
	/**
	 * Gets the general settings
	 * 
	 * @param array $setting_1, $setting_2, $setting_3, etc.
	 * @return array|bool
	 */
	public function get_settings() {
		global $user;
		
		// Get the settings
		$settings = func_get_args();
		
		// If they did pass in an array
		if( is_array( $settings[0] ) ) {
			$settings = $settings[0];
		} elseif( !is_array( $settings ) ) {
			return;
		}
		
		// Typecast
		$website_id = (int) $user['website']['website_id'];
		
		// Put the settings in a SQL format
		$sql_settings = '';
		
		foreach( $settings as $s ) {
			if( !empty( $sql_settings ) )
				$sql_settings .= ',';
			
			$sql_settings .= "'" . $this->db->escape( $s ) . "'";
		}
		
		// Get the settings
		$settings_array = $this->db->get_results( "SELECT `key`, `value` FROM `website_settings` WHERE `website_id` = $website_id AND `key` IN ($sql_settings) ORDER BY `key`", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get website_settings.', __LINE__, __METHOD__ );
			return false;
		}
		
		$new_settings = ar::assign_key( $settings_array, 'key', true );
		
		// @Fix should not loop queries
		// Now make sure they exist, if not, create them, and then run again
		foreach( $settings as $s ) {
			if( !array_key_exists( $s, $new_settings ) ) {
				$this->create_setting( $s );
				$new_settings[$s] = '';
			}
		}

		return $new_settings;
	}
	
	/**
	 * Creates a setting
	 * 
	 * @param string $key
	 * @param string $value (optional)
	 * @return bool
	 */
	public function create_setting( $key, $value = '' ) {
		global $user;
		
		$this->db->insert( 'website_settings', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'value' => $value ), 'iss' );
	
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to create website setting.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}
	
	/**
	 * Updates an associative array of settings
	 * 
	 * @param array $settings
	 * @return bool
	 */
	public function update_settings( $settings ) {
		global $user;
		
		// Typecast
		$website_id = (int) $user['website']['website_id'];
		
		// Prepare statement
		$statement = $this->db->prepare( "UPDATE `website_settings` SET `value` = ? WHERE `website_id` = $website_id AND `key` = ?" );
		$statement->bind_param( 'ss', $v, $k );
		
		foreach( $settings as $k => $v ) {
			$statement->execute();
			
			// Handle any error
			if( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( "Failed to update website's settings.", __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Create Page
	 *
	 * Adds a page to a website if the user has permissions 7 or higher
	 *
	 * @param string $slug
	 * @param string $title
	 * @return bool
	 */
	public function create_page( $slug, $title ) {
		global $user;
		
		if( $user['role'] < 8 )
			return false;
		
		// Insert the page
		$this->db->insert( 'website_pages', array( 'website_id' => $user['website']['website_id'], 'slug' => $slug, 'title' => $title, 'status' => 1, 'date_created' => date_time::date('Y-m-d H:i:s') ), 'issis' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to create website page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete
	 *
	 * @param int $website_page_id
	 * @return bool
	 */
	public function delete( $website_page_id ) {
		global $user;
		
		// Must have the proper role
		if( $user['role'] < 8 )
			return false;
		
		// Delete the website page
		$this->db->prepare( 'DELETE FROM `website_pages` WHERE `website_page_id` = ? AND `website_id` = ?', 'ii', $website_page_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to delete website page.', __LINE__, __METHOD__ );
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