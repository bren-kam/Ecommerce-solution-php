<?php
/**
 * Handles all the stuff for Share and Save
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Share_and_Save extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}

	/**
	 * Get Tab
	 *
	 * @param string $fb_page_id
	 * @param bool $liked
	 * @return string
	 */
	public function get_tab( $fb_page_id, $liked ) {
		// Determine the field
		if ( $liked ) {
			$fields = 'a.`after` AS content, a.`minimum`, a.`maximum`, a.`share_title`, a.`share_image_url`, a.`share_text`, COUNT( b.`email_id` ) AS total';
		} else {
			$fields = 'a.`before` AS content, a.`minimum`, a.`maximum`, COUNT( b.`email_id` ) AS total';
		}
		
		// Get the tab
		$tab = $this->db->prepare( "SELECT $fields FROM `sm_share_and_save` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_list_id` = b.`email_list_id` ) WHERE a.`fb_page_id` = ?", 's', $fb_page_id )->get_row( '', ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get tab.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $tab;
	}
	
	/**
	 * Adds an email to the appropriate categories
	 *
	 * @param int $fb_page_id
	 * @param string $name
	 * @param string $email
	 * @return bool
	 */
	public function add_email( $fb_page_id, $name, $email ) {
		$email = strtolower( $email );
		
		// We need to get the email_id
		$email_data = $this->db->prepare( 'SELECT a.`email_id`, b.`website_id`, IF( COUNT( d.`email_id` ) >= c.`maximum`, c.`maximum_email_list_id`, c.`email_list_id` ) AS email_list_id FROM `emails` AS a LEFT JOIN `sm_facebook_page` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `sm_share_and_save` AS c ON ( b.`id` = c.`sm_facebook_page_id` ) LEFT JOIN `email_associations` AS d ON ( c.`email_list_id` = d.`email_list_id` ) WHERE a.`email` = ? AND b.`status` = 1 AND c.`fb_page_id` = ?', 'ss', $email, $fb_page_id )->get_row( '', ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email data', __LINE__, __METHOD__ );
			return false;
		}

		// If there was no email, then grab the other fields
		if( !$email_data['email_id'] ) {
			// @Fix the above query should be able to grab the fields even if email_id is null
			
		 	// We need to get the email_id
			$email_data = $this->db->prepare( 'SELECT a.`website_id`, IF( COUNT( c.`email_id` ) >= b.`maximum`, b.`maximum_email_list_id`, b.`email_list_id` ) AS email_list_id FROM `sm_facebook_page` AS a LEFT JOIN `sm_share_and_save` AS b ON ( a.`id` = b.`sm_facebook_page_id` ) LEFT JOIN `email_associations` AS c ON ( b.`email_list_id` = c.`email_list_id` ) WHERE a.`status` = 1 AND b.`fb_page_id` = ?', 's', $fb_page_id )->get_row( '', ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get email data', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Make sure theyv'e set an email list id
		if( 0 == $email_data['email_list_id'] )
			return;
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email id', __LINE__, __METHOD__ );
			return false;
		}
		
		if( $email_data['email_id'] ) {
			// Type juggling for insertion later
			$email_id = (int) $email_data['email_id'];
			
			$this->db->update( 'emails', array( 'status' => 1 ), array( 'email_id' => $email_id ), 'i', 'i' );
		
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to update email', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			$this->db->insert( 'emails', array( 'website_id' => $email_data['website_id'], 'name' => $name, 'email' => $email, 'date_created' => date_time::date( 'Y-m-d H:i:s' ) ), 'isss' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to insert email', __LINE__, __METHOD__ );
				return false;
			}
			
			$email_id = (int) $this->db->insert_id;
		}
		
		// Declare variable
		$email_list_id = (int) $email_data['email_list_id'];
		
		// Get default email list id
		$default_email_list_id = (int) $this->db->prepare( 'SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = ? AND `category_id` = 0', 'i', $email_data['website_id'] )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get default email list id', __LINE__, __METHOD__ );
			return false;
		}
		
		$this->db->query( "INSERT INTO `email_associations` ( `email_id`, `email_list_id` ) VALUES ( $email_id, $default_email_list_id ), ( $email_id, $email_list_id ) ON DUPLICATE KEY UPDATE `email_id` = VALUES( `email_id` )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add email to lists', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Connect a website
	 *
	 * @param string $fb_page_id
	 * @param string $key
	 * @return array
	 */
	public function connect( $fb_page_id, $key ) {
		// Connect the websites
		$this->db->update( 'sm_share_and_save', array( 'fb_page_id' => $fb_page_id ), array( 'key' => $key ), 's', 's' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return array
	 */
	public function get_connected_website( $fb_page_id ) {
		// Type Juggling
		$fb_page_id = (int) $fb_page_id;
		
		// Get the connected website
		$website = $this->db->get_row( "SELECT a.`title`, c.`key` FROM `websites` AS a LEFT JOIN `sm_facebook_page` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `sm_share_and_save` AS c ON ( b.`id` = c.`sm_facebook_page_id` ) WHERE b.`status` = 1 AND c.`fb_page_id` = $fb_page_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
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