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
        parent::__construct( 'sm_posting_posts' );

        // We want to make sure they match
        if ( isset( $this->sm_posting_post_id ) )
            $this->id = $this->sm_posting_post_id;
    }

    /**
     * Get
     *
     * @param int $sm_posting_post_id
     * @param int $sm_facebook_page_id
     */
    public function get( $sm_posting_post_id, $sm_facebook_page_id ) {
        $this->prepare(
            'SELECT `sm_posting_post_id`, `sm_facebook_page_id`, `status` FROM `sm_posting_posts` WHERE `sm_posting_post_id` = :sm_posting_post_id AND `sm_facebook_page_id` = :sm_facebook_page_id'
            , 'ii'
            , array( ':sm_posting_post_id' => $sm_posting_post_id, ':sm_facebook_page_id' => $sm_facebook_page_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->sm_posting_post_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'sm_facebook_page_id' => $this->sm_facebook_page_id
            , 'access_token' => strip_tags($this->access_token)
            , 'post' => strip_tags($this->post)
            , 'link' => strip_tags($this->link)
            , 'status' => $this->status
            , 'date_posted' => strip_tags($this->date_posted)
            , 'date_created' => $this->date_created
        ), 'isssiss' );

        $this->id = $this->sm_posting_post_id = $this->get_insert_id();
    }

   /**
    * Get unposted posts
    *
    * @return SocialMediaPostingPost[]
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
        $this->update( array(
            'status' => $this->status
        ), array(
            'sm_posting_post_id' => $this->id
        ), 'i', 'i' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'sm_posting_post_id' => $this->id
            , 'sm_facebook_page_id' => $this->sm_facebook_page_id
        ), 'ii' );
    }

    /**
     * List
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return SocialMediaPostingPost[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `sm_posting_post_id`, `post`, `error`, `status`, `date_posted` FROM `sm_posting_posts` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'SocialMediaPostingPost' );
    }

    /**
     * Count all
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( `sm_posting_post_id` )  FROM `sm_posting_posts` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
