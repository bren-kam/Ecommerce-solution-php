<?php
/**
 * Handles all the social media
 *
 * @package Grey Suit Retail
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
		$posts = $this->db->get_results( "SELECT a.`sm_posting_post_id`, a.`access_token`, a.`post`, a.`link`, b.`fb_page_id`, c.`website_id`, d.`title` AS account, e.`email`, g.`name` AS company, g.`domain` FROM `sm_posting_posts` AS a LEFT JOIN `sm_posting` AS b ON ( a.`sm_facebook_page_id` = b.`sm_facebook_page_id` ) LEFT JOIN `sm_facebook_page` AS c ON ( b.`sm_facebook_page_id` = c.`id` ) LEFT JOIN `websites` AS d ON ( c.`website_id` = d.`website_id` ) LEFT JOIN `users` AS e ON ( d.`os_user_id` = e.`user_id` ) LEFT JOIN `users` AS f ON ( d.`user_id` = f.`user_id` ) LEFT JOIN `companies` AS g ON ( f.`company_id` = g.`company_id` ) WHERE a.`status` = 0 AND NOW() > a.`date_posted` AND c.`status` = 1", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the posts.', __LINE__, __METHOD__ );
			return false;
		}

        // Mark those posts as scheduled
        $this->db->query( 'UPDATE `sm_posting_posts` SET `status` = 1 WHERE `status` = 0 AND NOW() > `date_posted`' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to to mark posts as posted.', __LINE__, __METHOD__ );
			return false;
		}

		return $posts;
	}
	
	/**
	 * Mark Posting Post Errors
	 *
	 * @param array $sm_error_ids
	 * @return bool
	 */
	public function mark_posting_post_errors( $sm_error_ids ) {
		if ( !is_array( $sm_error_ids ) || 0 == count( $sm_error_ids ) )
			return false;
		
		// Prepare statement
		$statement = $this->db->prepare( 'UPDATE `sm_posting_posts` SET `status` = -1, `error` = ? WHERE `sm_posting_post_id` = ?' );
		$statement->bind_param( 'ss', $error_message, $sm_posting_post_id );
		
		// Make sure they are all integers
		foreach ( $sm_error_ids as $sm_posting_post_id => $error_message ) {
			$statement->execute();
			
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->_err( "Failed to update posting post's error.", __LINE__, __METHOD__ );
				return false;
			}
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
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}