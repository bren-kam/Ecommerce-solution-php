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
	 * Get Posting Posts
	 *
	 * @return array
	 */
	public function get_posting_posts() {
		// Get the posting posts
		$posts = $this->db->get_results( "SELECT a.`sm_posting_post_id`, a.`access_token`, a.`post`, a.`link`, b.`fb_page_id`, d.`email` FROM `sm_posting_posts` AS a LEFT JOIN `sm_posting` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `websites` AS c ON ( b.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( c.`os_user_id` = d.`user_id` ) WHERE a.`status` = 0 AND NOW() > a.`date_posted`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the posts.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $posts;
	}
	
	/**
	 * Get Posting Posts
	 *
	 * @param array $sm_posting_post_ids
	 * @return bool
	 */
	public function complete_posting_posts( $sm_posting_post_ids ) {
		if ( !is_array( $sm_posting_post_ids ) )
			return false;
		
		// Make sure they are all integers
		foreach ( $sm_posting_post_ids as &$id ) {
			$id = (int) $id;
		}
		
		$this->db->query( 'UPDATE `sm_posting_posts` SET `status` = 1 WHERE `sm_posting_post_id` IN(' . implode( ',', $sm_posting_post_ids ) . ')' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to complete the posting posts.', __LINE__, __METHOD__ );
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