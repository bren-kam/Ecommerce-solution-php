<?php
class WebsiteReach extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_reach_id, $website_id, $website_user_id, $assigned_to_user_id, $message, $waiting, $status
        , $assigned_to_date, $date_created, $priority;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_reaches' );

        // We want to make sure they match
        if ( isset( $this->website_reach_id ) )
            $this->id = $this->website_reach_id;
    }

    /**
	 * List Users
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return WebsiteReach[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        $order_by = ( !$order_by ) ? "ORDER BY website_reach_id DESC" : $order_by;

        return $this->prepare(
            "SELECT wr.`website_reach_id`, IF( 0 = wr.`assigned_to_user_id`, 'Unassigned', u.`contact_name` ) AS assigned_to, wr.`status`, wr.`priority`, wr.`date_created`, CONCAT( wu.`billing_first_name`, ' ', IF( wu.`billing_last_name`,  wu.`billing_last_name`, '' ) ) AS name, wu.`email`, IF( 1 = wr.`status` OR wrc.`website_reach_comment_id` IS NOT NULL AND wrc.`user_id` = wr.`assigned_to_user_id`, 0, 1 ) AS waiting, w.`title` AS website FROM `website_reaches` AS wr LEFT JOIN `website_users` AS wu ON ( wu.`website_user_id` = wr.`website_user_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = wr.`assigned_to_user_id` ) LEFT JOIN ( SELECT `website_reach_comment_id`, `website_reach_id`, `user_id` FROM `website_reach_comments` ORDER BY `website_reach_comment_id` DESC ) AS wrc ON ( wrc.`website_reach_id` = wr.`website_reach_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = wr.`website_id` ) WHERE 1" . $where . " GROUP BY wr.`website_reach_id` $order_by, wrc.`website_reach_comment_id` DESC LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteReach' );
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
            "SELECT COUNT( DISTINCT wr.`website_reach_id` ) FROM `website_reaches` AS wr LEFT JOIN `website_users` AS wu ON ( wu.`website_user_id` = wr.`website_user_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = wr.`assigned_to_user_id` ) LEFT JOIN ( SELECT `website_reach_comment_id`, `website_reach_id`, `user_id` FROM `website_reach_comments` ORDER BY `website_reach_comment_id` DESC ) AS wrc ON ( wrc.`website_reach_id` = wr.`website_reach_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = wr.`website_id` ) WHERE 1" . $where
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
