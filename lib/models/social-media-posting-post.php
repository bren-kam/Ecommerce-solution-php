<?php
class SocialMediaPostingPost extends ActiveRecordBase {
    public $id, $sm_posting_post_id, $sm_facebook_page_id, $access_token, $post, $link, $error, $status, $date_posted, $date_created;

    /**
     * Hold variables for other tables
     */
    public $fb_page_id, $website_id, $account, $email, $company, $domain;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_post' );

        // We want to make sure they match
        if ( isset( $this->sm_posting_post_id ) )
            $this->id = $this->sm_posting_post_id;
    }

   /**
    * Get unposted posts
    *
    * @return array
    */
    public function get_unposted_posts() {
        // Get the posting posts
		return $this->get_results(
            "SELECT spp.`sm_posting_post_id`, spp.`access_token`, spp.`post`, spp.`link`, sp.`fb_page_id`, sfp.`website_id`, w.`title` AS account, u.`email`, c.`name` AS company, c.`domain` FROM `sm_posting_posts` AS spp LEFT JOIN `sm_posting` AS sp ON ( sp.`sm_facebook_page_id` = spp.`sm_facebook_page_id` ) LEFT JOIN `sm_facebook_page` AS sfp ON ( sfp.`id` = sp.`sm_facebook_page_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = sfp.`website_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = w.`os_user_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = w.`user_id` ) LEFT JOIN `companies` AS c ON ( c.`company_id` = u2.`company_id` ) WHERE spp.`status` = 0 AND NOW() > spp.`date_posted` AND sfp.`status` = 1"
            , PDO::FETCH_CLASS
            , 'SocialMediaPostingPost'
        );
    }

    /**
	 * Mark Errors
	 *
	 * @param array $sm_errors
	 * @return bool
	 */
	public function mark_errors( array $sm_errors ) {
	    // Prepare statement
		$statement = $this->prepare_raw( 'UPDATE `sm_posting_posts` SET `status` = -1, `error` = :error WHERE `sm_posting_post_id` = :sm_posting_post_id' );
		$statement->bind_param( ':error', $error, 's' )
		    ->bind_param( ':sm_posting_post_id', $sm_posting_post_id, 'i' );

		// Loop through the statement and update anything as it needs to be updated
		foreach ( $sm_errors as $sm_posting_post_id => $error ) {
			$statement->query();
		}
	}

    /**
     * Save
     */
    public function save() {
        parent::update( array(
            'status' => $this->status
        ), array(
            'sm_posting_post_id' => $this->id
        ), 'i', 'i' );
    }
}
