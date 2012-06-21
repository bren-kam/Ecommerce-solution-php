<?php
/**
 * Handles all the stuff for Posting
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Posting extends Base_Class {
	/**
	 * The connected website_id
	 *
	 * @var int
	 */
	private $website_id = 0;
	
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Connect a website
	 *
	 * @param int $fb_user_id
	 * @param int $fb_page_id
	 * @param string $key
	 * @param string $access_token
	 * @return array
	 */
	public function connect( $fb_user_id, $fb_page_id, $key, $access_token ) {
		// Connect the websites
		$this->db->update( 'sm_posting', array( 'fb_user_id' => $fb_user_id, 'fb_page_id' => $fb_page_id, 'access_token' => $access_token ), array( 'key' => $key ), 'iis', 's' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Connect a website
	 *
	 * @param int $fb_user_id
	 * @return bool
	 */
	public function connected( $fb_user_id ) {
		// Type Juggling
		$fb_user_id = (int) $fb_user_id;
		
		// See if there is a website_id associated with the user
		$this->website_id = $this->db->get_var( "SELECT a.`website_id` FROM `sm_facebook_page` AS a LEFT JOIN `sm_posting` AS b ON ( a.`id` = b.`sm_facebook_page_id` ) WHERE a.`status` = 1 AND b.`fb_user_id` = $fb_user_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to check if website is connected.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->website_id > 0;
	}
	
	/**
	 * Get Connected Website
	 *
	 * @param int $fb_user_id
	 * @return array
	 */
	public function get_connected_pages( $fb_user_id ) {
		// Type Juggling
		$fb_user_id = (int) $fb_user_id;
		
		// See if there is a website_id associated with the user
		$fb_page_ids = $this->db->get_col( "SELECT `fb_page_id` FROM `sm_posting` WHERE `fb_user_id` = $fb_user_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get connected pages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $fb_page_ids;
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