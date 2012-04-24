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
		$posts = $this->db->get_results( "SELECT a.`sm_posting_post_id`, a.`access_token`, a.`post`, a.`link`, b.`fb_page_id`, c.`website_id`, c.`title` AS account, d.`email`, f.`name` AS company, f.`domain` FROM `sm_posting_posts` AS a LEFT JOIN `sm_posting` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `websites` AS c ON ( b.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( c.`os_user_id` = d.`user_id` ) LEFT JOIN `users` AS e ON ( c.`user_id` = e.`user_id` ) LEFT JOIN `companies` AS f ON ( e.`company_id` = f.`company_id` ) WHERE a.`status` = 0 AND NOW() > a.`date_posted`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get the posts.', __LINE__, __METHOD__ );
			return false;
		}

        // Mark those posts as scheduled
        $this->db->query( 'UPDATE `sm_posting_posts` SET `status` = 1 WHERE `status` = 0 AND NOW() > `date_posted`' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to to mark posts as posted.', __LINE__, __METHOD__ );
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
				$this->err( "Failed to update posting post's error.", __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}

    /**
     * Reset Social Media Account
     *
     * @param int $website_id
     * @param string $social_media
     * @return bool
     */
    public function reset( $website_id, $social_media ) {
        // Type Juggling
        $website_id = (int) $website_id;

        // Define variables
        $delete = false;

        switch ( $social_media ) {
            case 'email-sign-up':
                $table = 'sm_email_sign_up';
            break;

            case 'fan-offer':
                $table = 'sm_fan_offer';
            break;

            case 'sweepstakes':
                $table = 'sm_sweepstakes';
            break;

            case 'share-and-save':
                $table = 'sm_share_and_save';
            break;

            case 'facebook-site':
                $table = 'sm_facebook_site';
            break;

            case 'contact-us':
                $table = 'sm_contact_us';

                $delete = true;
            break;

            case 'about-us':
                $table = 'sm_about_us';

                $delete = true;
            break;

            case 'products':
                $table = 'sm_products';
            break;

            case 'current-ad':
                $table = 'sm_current_ad';

                $delete = true;
            break;

            case 'posting':
                $this->db->update( 'sm_posting', array( 'fb_user_id' => 0, 'fb_page_id' => 0, 'access_token' => '' ), array( 'website_id' => $website_id ), 'iis', 'i' );

                // Handle any error
                if ( $this->db->errno() ) {
                    $this->err( 'Failed to reset Social Media - Posting.', __LINE__, __METHOD__ );
                    return false;
                }

                return true;
            break;
        }

        if ( $delete ) {
            $this->db->query( "DELETE FROM `$table` WHERE `website_id` = $website_id" );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->err( "Failed to delete Social Media - $social_media.", __LINE__, __METHOD__ );
                return false;
            }

            return true;
        }

        $this->db->update( $table, array( 'fb_page_id' => '' ), array( 'website_id' => $website_id ), 's', 'i' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( "Failed to to reset Social Media - $social_media.", __LINE__, __METHOD__ );
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
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}