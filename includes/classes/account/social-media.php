<?php
/**
 * Handles all the social media
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Social_Media extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/***** EMAIL SIGN UP *****/
	
	/**
	 * Create Email Sign Up
	 *
	 * @return string
	 */
	public function create_email_sign_up() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_email_sign_up', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create email sign up.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Email Sign Up
	 *
	 * @return array
	 */
	public function get_email_sign_up() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the email sign up
		$email_sign_up = $this->db->get_row( "SELECT `fb_page_id`, `email_list_id`, `key`, `tab` FROM `sm_email_sign_up` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the email sign up.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email_sign_up;
	}
	
	/**
	 * Update Email Sign Up
	 *
	 * @param string $tab
	 * @param int $email_list_id
	 * @return bool
	 */
	public function update_email_sign_up( $tab, $email_list_id ) {
		global $user;
		
		// Update the email sign up
		$this->db->update( 'sm_email_sign_up', array( 'tab' => $tab, 'email_list_id' => $email_list_id ), array( 'website_id' => $user['website']['website_id'] ), 'si', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update email sign up.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** FAN OFFER *****/
	
	/**
	 * Create Fan Offer
	 *
	 * @return string
	 */
	public function create_fan_offer() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_fan_offer', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create fan offer.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Fan Offer
	 *
	 * @return array
	 */
	public function get_fan_offer() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the fan offer
		$fan_offer = $this->db->get_row( "SELECT `fb_page_id`, `email_list_id`, `key`, `before`, `after`, UNIX_TIMESTAMP( `start_date` ) AS start_date, UNIX_TIMESTAMP( `end_date` ) AS end_date, `share_title`, `share_image_url`, `share_text` FROM `sm_fan_offer` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the fan offer.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $fan_offer;
	}
	
	/**
	 * Update Fan Offer
	 *
	 * @param int $email_list_id
	 * @param string $before
	 * @param string $after
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $share_title
	 * @param string $share_image_url
	 * @param string $share_text
	 * @return bool
	 */
	public function update_fan_offer( $email_list_id, $before, $after, $start_date, $end_date, $share_title, $share_image_url, $share_text ) {
		global $user;
		
		// Update the fan offer
		$this->db->update( 'sm_fan_offer', array( 'email_list_id' => $email_list_id, 'before' => $before, 'after' => $after, 'start_date' => $start_date, 'end_date' => $end_date, 'share_title' => $share_title, 'share_image_url' => $share_image_url, 'share_text' => $share_text ), array( 'website_id' => $user['website']['website_id'] ), 'isssssss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update fan offer.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** SWEEPSTAKES *****/
	
	/**
	 * Create Sweepstakes
	 *
	 * @return string
	 */
	public function create_sweepstakes() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_sweepstakes', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create sweepstakes.', __LINE__, __METHOD__ );
			return false;
		}
		
		echo 'here';
		
		return $key;
	}
	
	/**
	 * Get Sweepstakes
	 *
	 * @return array
	 */
	public function get_sweepstakes() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the sweepstakes
		$sweepstakes = $this->db->get_row( "SELECT `fb_page_id`, `email_list_id`, `key`, `before`, `after`, UNIX_TIMESTAMP( `start_date` ) AS start_date, UNIX_TIMESTAMP( `end_date` ) AS end_date, `contest_rules_url`, `share_title`, `share_image_url`, `share_text` FROM `sm_sweepstakes` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the sweepstakes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $sweepstakes;
	}
	
	/**
	 * Update Sweepstakes
	 *
	 * @param int $email_list_id
	 * @param string $before
	 * @param string $after
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $contest_rules_url
	 * @param string $share_title
	 * @param string $share_image_url
	 * @param string $share_text
	 * @return bool
	 */
	public function update_sweepstakes( $email_list_id, $before, $after, $start_date, $end_date, $contest_rules_url, $share_title, $share_image_url, $share_text ) {
		global $user;
		
		// Update the sweepstakes
		$this->db->update( 'sm_sweepstakes', array( 'email_list_id' => $email_list_id, 'before' => $before, 'after' => $after, 'start_date' => $start_date, 'end_date' => $end_date, 'contest_rules_url' => $contest_rules_url, 'share_title' => $share_title, 'share_image_url' => $share_image_url, 'share_text' => $share_text ), array( 'website_id' => $user['website']['website_id'] ), 'issssssss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update sweepstakes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** SHARE AND SAVE *****/
	
	/**
	 * Create Share and Save
	 *
	 * @return string
	 */
	public function create_share_and_save() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_share_and_save', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Share and Save
	 *
	 * @return array
	 */
	public function get_share_and_save() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the fan offer
		$share_and_save = $this->db->get_row( "SELECT `fb_page_id`, `email_list_id`, `maximum_email_list_id`, `key`, `before`, `after`, `minimum`, `maximum`, `share_title`, `share_image_url`, `share_text` FROM `sm_share_and_save` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $share_and_save;
	}
	
	/**
	 * Update Share and Save
	 *
	 * @param int $email_list_id
	 * @param int $maximum_email_list_id
	 * @param string $before
	 * @param string $after
	 * @param int $minimum
	 * @param int $maximum
	 * @param string $share_title
	 * @param string $share_image_url
	 * @param string $share_text
	 * @return bool
	 */
	public function update_share_and_save( $email_list_id, $maximum_email_list_id, $before, $after, $minimum, $maximum, $share_title, $share_image_url, $share_text ) {
		global $user;
		
		// Update the share and save
		$this->db->update( 'sm_share_and_save', array( 'email_list_id' => $email_list_id, 'maximum_email_list_id' => $maximum_email_list_id, 'before' => $before, 'after' => $after, 'minimum' => $minimum, 'maximum' => $maximum, 'share_title' => $share_title, 'share_image_url' => $share_image_url, 'share_text' => $share_text ), array( 'website_id' => $user['website']['website_id'] ), 'iissiisss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** FACEBOOK SITE *****/
	
	/**
	 * Create Facebook Site
	 *
	 * @return string
	 */
	public function create_facebook_site() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_facebook_site', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create facebook site.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Facebook Site
	 *
	 * @return array
	 */
	public function get_facebook_site() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the facebook site
		$facebook_site = $this->db->get_row( "SELECT `fb_page_id`, `key`, `content` FROM `sm_facebook_site` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the facebook site.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $facebook_site;
	}
	
	/**
	 * Update The Facebook Site Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_facebook_site( $content ) {
		global $user;
		
		// Update the facebook site
		$this->db->update( 'sm_facebook_site', array( 'content' => $content ), array( 'website_id' => $user['website']['website_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update the facebook site.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** CONTACT US *****/
	
	/**
	 * Create Contact Us
	 *
	 * @return string
	 */
	public function create_contact_us() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		// Get the page id for their contact us page if they have it
		if ( $user['website']['pages'] ) {
			// @Fix should a custom method be used or query string to get JUST the website page id?
			$w = new Websites;
			$page = $w->get_page_by_slug('contact-us');
			
			$website_page_id = $page['website_page_id'];
		} else {
			$website_page_id = 0;
		}
		
		$this->db->insert( 'sm_contact_us', array( 'website_id' => $user['website']['website_id'], 'website_page_id' => $website_page_id, 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create contact us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Contact Us
	 *
	 * @return array
	 */
	public function get_contact_us() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the contact us
		$contact_us = $this->db->get_row( "SELECT `fb_page_id`, `key`, `content` FROM `sm_contact_us` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the contact us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $contact_us;
	}
	
	/**
	 * Update The Contact Us Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_contact_us( $content ) {
		global $user;
		
		// Update the contact us page
		$this->db->update( 'sm_contact_us', array( 'content' => $content ), array( 'website_id' => $user['website']['website_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update the contact us page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** ABOUT US *****/
	
	/**
	 * Create About Us
	 *
	 * @return string
	 */
	public function create_about_us() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		// Get the page id for their About Us page if they have it
		if ( $user['website']['pages'] ) {
			// @Fix should a custom method be used or query string to get JUST the website page id?
			$w = new Websites;
			$page = $w->get_page_by_slug('about-us');
			
			$website_page_id = $page['website_page_id'];
		} else {
			$website_page_id = 0;
		}
		
		$this->db->insert( 'sm_about_us', array( 'website_id' => $user['website']['website_id'], 'website_page_id' => $website_page_id, 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create about us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get About Us
	 *
	 * @return array
	 */
	public function get_about_us() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the About Us
		$about_us = $this->db->get_row( "SELECT `fb_page_id`, `key`, `content` FROM `sm_about_us` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the about us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $about_us;
	}
	
	/**
	 * Update The About Us Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_about_us( $content ) {
		global $user;
		
		// Update the About Us page
		$this->db->update( 'sm_about_us', array( 'content' => $content ), array( 'website_id' => $user['website']['website_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update the about us page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** PRODUCTS *****/
	
	/**
	 * Create Products
	 *
	 * @return string
	 */
	public function create_products() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_products', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Products
	 *
	 * @return array
	 */
	public function get_products() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the products page
		$products = $this->db->get_row( "SELECT `fb_page_id`, `key`, `content` FROM `sm_products` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Update The Products Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_products( $content ) {
		global $user;
		
		// Update the products page
		$this->db->update( 'sm_products', array( 'content' => $content ), array( 'website_id' => $user['website']['website_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update the products page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** CURRENT AD *****/
	
	/**
	 * Create Current Ad
	 *
	 * @return string
	 */
	public function create_current_ad() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		// Get the page id for their Current Ad page if they have it
		if ( $user['website']['pages'] ) {
			// @Fix should a custom method be used or query string to get JUST the website page id?
			$w = new Websites;
			$page = $w->get_page_by_slug('sidebar');
			
			$website_page_id = $page['website_page_id'];
		} else {
			$website_page_id = 0;
		}
		
		$this->db->insert( 'sm_current_ad', array( 'website_id' => $user['website']['website_id'], 'website_page_id' => $website_page_id, 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create current ad.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Current Ad
	 *
	 * @return array
	 */
	public function get_current_ad() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the Current Ad
		$current_ad = $this->db->get_row( "SELECT `fb_page_id`, `key`, `content` FROM `sm_current_ad` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the current ad.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $current_ad;
	}
	
	/**
	 * Update The Current Ad Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_current_ad( $content ) {
		global $user;
		
		// Update the Current Ad page
		$this->db->update( 'sm_current_ad', array( 'content' => $content ), array( 'website_id' => $user['website']['website_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update the current ad page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** ANALYTICS *****/
	
	/**
	 * Create Analytics
	 *
	 * @return string
	 */
	public function create_analytics() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_analytics', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create analytics.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Analytics
	 *
	 * @return array
	 */
	public function get_analytics() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the About Us
		$analytics = $this->db->get_row( "SELECT `key`, `token` FROM `sm_analytics` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the analytics.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $analytics;
	}
	
	/***** AUTO POSTING *****/
	
	/**
	 * Create Auto Posting
	 *
	 * @return string
	 */
	public function create_auto_posting() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $user['website']['website_id'] );
		
		$this->db->insert( 'sm_auto_posting', array( 'website_id' => $user['website']['website_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Auto Posting
	 *
	 * @return array
	 */
	public function get_auto_posting() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the auto posting
		$auto_posting = $this->db->get_row( "SELECT `fb_user_id`, `key`, `access_token` FROM `sm_auto_posting` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the auto posting.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $auto_posting;
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