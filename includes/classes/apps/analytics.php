<?php
/**
 * Handles all the stuff for Analytics
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Analytics extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Connect a page
	 *
	 * @param int $fb_page_id
	 * @param string $token
	 * @param string $key
	 * @return array
	 */
	public function connect( $fb_page_id, $token, $key ) {
		// Connect the websites
		$this->db->update( 'sm_analytics', array( 'fb_page_id' => $fb_page_id, 'token' => $token ), array( 'key' => $key ), 'is', 's' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to connect page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Remove a connection
	 *
	 * @param int $fb_page_id
	 * @param string $token
	 * @return array
	 */
	public function remove_connection( $fb_page_id, $token ) {
		// Remove the connection
		$this->db->update( 'sm_analytics', array( 'fb_page_id' => '', 'token' => '' ), array( 'fb_page_id' => $fb_page_id, 'token' => $token ), 'is', 'is' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to remove connection.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Connected Pages
	 *
	 * @param string $token
	 * @return array
	 */
	public function get_connected_pages( $token ) {
		// Get the connected pages
		$pages = $this->db->prepare( 'SELECT a.`title`, b.`fb_page_id` FROM `websites` AS a LEFT JOIN `sm_analytics` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`token` = ?', 's', $token )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get connected pages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $pages;
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