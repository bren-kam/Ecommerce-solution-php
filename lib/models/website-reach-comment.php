<?php
class WebsiteReachComment extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_reach_comment_id, $website_reach_id, $website_user_id, $user_id, $comment, $private
        , $date_created;

    // Artificial Fields
    public $contact_name, $website_user_name;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_reach_comments' );

        // We want to make sure they match
        if ( isset( $this->website_reach_comment_id ) )
            $this->id = $this->website_reach_comment_id;
    }

    /**
     * Create
     */
    public function create() {
        // Set the time it was created
        $this->date_created = dt::now();

        $this->id = $this->website_reach_comment_id = $this->insert( array(
            'website_reach_id' => $this->website_reach_id
            , 'user_id' => $this->user_id
            , 'website_user_id' => $this->website_user_id
            , 'comment' => strip_tags($this->comment)
            , 'private' => $this->private
            , 'date_created' => $this->date_created
        ), 'iiisis' );
    }

    /**
     * Get
     *
     * @param int $website_reach_comment_id
     * @param int $account_id
     */
    public function get( $website_reach_comment_id, $account_id ) {
        $this->prepare(
            "SELECT wrc.`website_reach_comment_id` FROM `website_reach_comments` AS wrc LEFT JOIN `website_reaches` AS wr ON ( wr.`website_reach_id` = wrc.`website_reach_id` ) WHERE wrc.`website_reach_comment_id` = :website_reach_comment_id AND wr.`website_id` = :account_id ORDER BY wrc.`date_created` DESC"
            , 'ii'
            , array( ':website_reach_comment_id' => $website_reach_comment_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_reach_comment_id;
    }

    /**
     * Get By Reach
     *
     * @param int $website_reach_id
     * @param int $account_id
     * @return WebsiteReachComment[]
     */
    public function get_by_reach( $website_reach_id, $account_id ) {
        return $this->prepare(
            "SELECT wrc.`website_reach_comment_id`, wrc.`website_user_id`, wrc.`user_id`, wrc.`comment`, wrc.`private`, wrc.`date_created`, u.`contact_name`, wu.`billing_name` as website_user_name
              FROM `website_reach_comments` AS wrc
              LEFT JOIN `users` AS u ON ( u.`user_id` = wrc.`user_id` )
              LEFT JOIN `website_reaches` AS wr ON ( wr.`website_reach_id` = wrc.`website_reach_id` )
              LEFT JOIN `website_users` AS wu ON ( wu.`website_user_id` = wrc.`website_user_id` )
              WHERE wrc.`website_reach_id` = :website_reach_id AND wr.`website_id` = :account_id
              ORDER BY wrc.`date_created` DESC"
            , 'ii'
            , array( ':website_reach_id' => $website_reach_id, ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteReachComment' );
    }

    /**
     * Delete the comment
     */
    public function remove() {
        $this->delete( array(
            'website_reach_comment_id' => $this->id
        ), 'i' );
    }
}
