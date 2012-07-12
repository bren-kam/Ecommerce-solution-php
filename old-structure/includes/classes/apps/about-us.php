<?php
/**
 * Handles all the stuff for About Us
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class About_Us extends Base_Class {
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
	 * @return string
	 */
	public function get_tab( $fb_page_id ) {
		// Get the tab
		$tab_data = $this->db->prepare( 'SELECT IF( 0 = `website_page_id`, `content`, 0 ) AS content, `website_page_id` FROM `sm_about_us` WHERE `fb_page_id` = ?', 's', $fb_page_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get tab.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( 0 != $tab_data['website_page_id'] ) {
			// If there was a website page id, we need to get the content from elsewhere
			$page = $this->db->prepare( "SELECT a.`title`, a.`content`, b.`domain` FROM `website_pages` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `sm_facebook_page` AS c ON ( b.`website_id` = c.`website_id` ) LEFT JOIN `sm_about_us` AS d ON ( c.`id` = d.`sm_facebook_page_id` ) WHERE a.`website_page_id` = ? AND c.`status` = 1 AND d.`fb_page_id` = ?", 'is', $tab_data['website_page_id'], $fb_page_id )->get_row( '', ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get website page.', __LINE__, __METHOD__ );
				return false;
			}

			// Form Tab
			$tab = '<h1>' . $page['title'] . '</h1>' . html_entity_decode( $page['content'], ENT_QUOTES, 'UTF-8' );
		} else {
			$tab = $tab_data['content'];
		}

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
		$this->db->update( 'sm_about_us', array( 'fb_page_id' => $fb_page_id ), array( 'key' => $key ), 's', 's' );
		
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
	 * @param int $fb_user_id
	 * @return array
	 */
	public function get_connected_website( $fb_page_id ) {
		// Type Juggling
		$fb_page_id = (int) $fb_page_id;
		
		// Get the connected website
		$website = $this->db->get_row( "SELECT a.`title`, c.`key` FROM `websites` AS a LEFT JOIN `sm_facebook_page` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `sm_about_us` AS c ON ( b.`id` = c.`sm_facebook_page_id` ) WHERE c.`fb_page_id` = $fb_page_id", ARRAY_A );
		
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