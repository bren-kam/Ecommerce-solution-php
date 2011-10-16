<?php
/**
 * Handles all the stuff for Sweepstakes
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Sweepstakes extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get Tab
	 *
	 * @param int $fb_page_id
	 * @param bool $liked
	 * @return string
	 */
	public function get_tab( $fb_page_id, $liked ) {
		// Type Juggling
		$fb_page_id = (int) $fb_page_id;
		
		// Determine the field
		$fields = ( $liked ) ? "`after` AS content, `contest_rules_url`, IF ( NOW() > `start_date` AND NOW() < `end_date`, 1, 0 ) AS valid, `share_title`, `share_image_url`, `share_text`" : '`before` AS content';
		
		// Get the tab
		$tab = $this->db->get_row( "SELECT {$fields} FROM `sm_sweepstakes` WHERE `fb_page_id` = $fb_page_id", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get tab.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add on the Sweepstakes Rules URL if it's not empty
		if ( isset( $tab['contest_rules_url'] ) && !empty( $tab['contest_rules_url'] ) )
			$tab['content'] .= '<p><a href="' . $tab['contest_rules_url'] . '" title="' . _('View Sweepstakes Rules') . '" target="_blank">' . _('View Sweepstakes Rules') . '</a></p>';
		
		return $tab;
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
		$this->db->update( 'sm_sweepstakes', array( 'fb_page_id' => $fb_page_id ), array( 'key' => $key ), 's', 's' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Connected Website
	 *
	 * @param int $fb_user_id
	 * @return array
	 */
	public function get_connected_website( $fb_page_id ) {
		// Type Juggling
		$fb_page_id = (int) $fb_page_id;
		
		// Get the connected website
		$website = $this->db->get_row( "SELECT a.`title`, b.`key` FROM `websites` AS a LEFT JOIN `sm_sweepstakes` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`fb_page_id` = $fb_page_id", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
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
		$email_data = $this->db->prepare( 'SELECT a.`email_id`, b.`website_id`, b.`email_list_id` FROM `emails` AS a LEFT JOIN `sm_sweepstakes` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`email` = ? AND b.`fb_page_id` = ?', 'ss', $email, $fb_page_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get email data', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there was no email, then grab the other fields
		if( !$email_data ) {
			// @Fix the above query should be able to grab the fields even if email_id is null
			
		 	// We need to get the email_id
			$email_data = $this->db->prepare( 'SELECT `website_id`, `email_list_id` FROM `sm_sweepstakes` WHERE `fb_page_id` = ?', 's', $fb_page_id )->get_row( '', ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get email data', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Make sure theyv'e set an email list id
		if( 0 == $email_data['email_list_id'] )
			return;
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get email id', __LINE__, __METHOD__ );
			return false;
		}
		
		// Type juggling and defining variable email list id
		$email_list_id = (int) $email_data['email_list_id'];
		
		if( $email_data['email_id'] ) {
			// Type juggling for insertion later
			$email_id = (int) $email_data['email_id'];
			
			$this->db->update( 'emails', array( 'status' => 1 ), array( 'email_id' => $email_id ), 'i', 'i' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to update email', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			$this->db->insert( 'emails', array( 'website_id' => $email_data['website_id'], 'name' => $name, 'email' => $email, 'date_created' => date_time::date( 'Y-m-d H:i:s' ) ), 'isss' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to insert email', __LINE__, __METHOD__ );
				return false;
			}
			
			$email_id = (int) $this->db->insert_id;
		}
		
		// Get default email list id
		$default_email_list_id = (int) $this->db->prepare( 'SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = ? AND `category_id` = 0', 'i', $email_data['website_id'] )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get default email list id', __LINE__, __METHOD__ );
			return false;
		}
		
		$this->db->query( "INSERT INTO `email_associations` ( `email_id`, `email_list_id` ) VALUES ( $email_id, $default_email_list_id ), ( $email_id, $email_list_id ) ON DUPLICATE KEY UPDATE `email_id` = VALUES( `email_id` )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add email to lists', __LINE__, __METHOD__ );
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