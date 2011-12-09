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
	
	/**
	 * Get Auto Posting Posts
	 *
	 * @return array
	 */
	public function get_auto_posting_posts() {
		// Get the auto posting posts
		$posts = $this->db->get_results( "SELECT a.`sm_auto_posting_post_id`, a.`access_token`, a.`post`, a.`link`, b.`fb_page_id` FROM `sm_auto_posting_posts` AS a LEFT JOIN `sm_auto_posting` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`status` = 0 AND NOW() > a.`date_posted`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the posts.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $posts;
	}
	
	/**
	 * Get Auto Posting Posts
	 *
	 * @param array $sm_auto_posting_post_ids
	 * @return bool
	 */
	public function complete_auto_posting_posts( $sm_auto_posting_post_ids ) {
		if ( !is_array( $sm_auto_posting_post_ids ) )
			return false;
		
		// Make sure they are all integers
		foreach ( $sm_auto_posting_post_ids as &$id ) {
			$id = (int) $id;
		}
		
		$this->db->query( 'UPDATE `sm_auto_posting_posts` SET `status` = 1 WHERE `sm_auto_posting_post_id` IN(' . implode( ',', $sm_auto_posting_post_ids ) . ')' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to complete the auto posting posts.', __LINE__, __METHOD__ );
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