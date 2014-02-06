<?php
class WebsiteReach extends ActiveRecordBase {
    const STATUS_OPEN = 0;
    const STATUS_CLOSED = 1;

    // The columns we will have access to
    public $id, $website_reach_id, $website_id, $website_user_id, $assigned_to_user_id, $message, $waiting, $status
        , $assigned_to_date, $date_created, $priority;

    // Artificial columns
    public $meta, $info, $product, $sku, $name, $email;

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
     * Get
     *
     * @param int $website_reach_id
     * @param int $account_id
     */
    public function get( $website_reach_id, $account_id ) {
        $this->prepare(
            "SELECT wr.`website_reach_id`, wr.`website_user_id`, wr.`assigned_to_user_id`, wr.`message`, wr.`priority`, wr.`status`, wr.`assigned_to_date`, wr.`date_created`, wr.`waiting`, IF( '' = wu.`billing_name` OR wu.`billing_name` IS NULL, CONCAT( wu.`billing_first_name`, ' ', IF( wu.`billing_last_name`,  wu.`billing_last_name`, '' ) ), wu.`billing_name` ) AS name, wu.`email`, w.`website_id`, w.`title` AS website, w.`domain`, COALESCE( u.`role`, 7 ) AS role FROM `website_reaches` AS wr LEFT JOIN `website_users` AS wu ON ( wu.`website_user_id` = wr.`website_user_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = wr.`website_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = wr.`assigned_to_user_id` ) WHERE wr.`website_reach_id` = :website_reach_id AND wr.`website_id` = :account_id"
            , 'ii'
            , array( ':website_reach_id' => $website_reach_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_reach_id;
    }

    /**
     * Get
     *
     * @param int $website_reach_id
     */
    public function get_by_id( $website_reach_id ) {
        $this->prepare(
            "SELECT wr.`website_reach_id`, wr.`website_user_id`, wr.`assigned_to_user_id`, wr.`message`, wr.`priority`, wr.`status`, wr.`assigned_to_date`, wr.`date_created`, wr.`waiting`, IF( '' = wu.`billing_name` OR wu.`billing_name` IS NULL, CONCAT( wu.`billing_first_name`, ' ', IF( wu.`billing_last_name`,  wu.`billing_last_name`, '' ) ), wu.`billing_name` ) AS name, wu.`email`, w.`website_id`, w.`title` AS website, w.`domain`, COALESCE( u.`role`, 7 ) AS role FROM `website_reaches` AS wr LEFT JOIN `website_users` AS wu ON ( wu.`website_user_id` = wr.`website_user_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = wr.`website_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = wr.`assigned_to_user_id` ) WHERE wr.`website_reach_id` = :website_reach_id"
            , 'i'
            , array( ':website_reach_id' => $website_reach_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_reach_id;
    }

    /**
     * Get Meta
     */
    public function get_meta() {
        // This will be expanded in the future as the meta is further developed
        $this->meta = ar::assign_key( $this->prepare(
            'SELECT `key`, `value` FROM `website_reach_meta` WHERE `website_reach_id` = :website_reach_id'
            , 'i'
            , array( ':website_reach_id' => $this->id )
        )->get_results( PDO::FETCH_ASSOC ), 'key', true );
    }

    /**
     * Get Info
     */
    public function get_info() {
        if ( !isset( $this->meta ) )
            $this->get_meta();
        
        switch ( $this->meta['type'] ) {
            case 'quote':
                $link = $this->meta['product-link'];
                $this->info['Product'] = '<a href="' . $link . '" title="' . $this->meta['product-name'] . '" target="_blank">' . $this->meta['product-name'] . '</a>';
                $this->info['SKU'] = '<a href="' . $link . '" title="' . $this->meta['product-sku'] . '" target="_blank">' . $this->meta['product-sku'] . '</a>';
                $this->info['Brand'] = $this->meta['product-brand'];

                if ( isset( $this->meta['location'] ) )
                    $this->info['Location'] = $this->meta['location'];
            break;
        }
    }

    /**
     * Returns a reach type meta in a human readable format
     *
     * @return string
     */
    public function get_friendly_type() {
        $type = _('Reach');

        switch( $this->meta['type'] ) {
            case 'quote':
                $type = _('Quote');
            break;
        }

        return $type;
    }

    /**
     * Save
     */
    public function save() {
        parent::update(
            array(
                'assigned_to_user_id' => $this->assigned_to_user_id
                , 'waiting' => $this->waiting
                , 'status' => $this->status
                , 'priority' => $this->priority
            ), array( 'website_reach_id' => $this->id )
            , 'iiii'
            , 'i'
        );
    }

    /**
	 * List
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return WebsiteReach[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        $order_by = ( !$order_by ) ? "ORDER BY website_reach_id DESC" : $order_by;

        return $this->prepare(
            "SELECT wr.`website_reach_id`, IF( 0 = wr.`assigned_to_user_id`, 'Unassigned', u.`contact_name` ) AS assigned_to, wr.`status`, wr.`priority`, wr.`date_created`, IF( '' = wu.`billing_name` OR wu.`billing_name` IS NULL, CONCAT( wu.`billing_first_name`, ' ', IF( wu.`billing_last_name`,  wu.`billing_last_name`, '' ) ), wu.`billing_name` ) AS name, wu.`email`, IF( 1 = wr.`status` OR wrc.`website_reach_comment_id` IS NOT NULL AND wrc.`user_id` = wr.`assigned_to_user_id`, 0, 1 ) AS waiting, w.`title` AS website FROM `website_reaches` AS wr LEFT JOIN `website_users` AS wu ON ( wu.`website_user_id` = wr.`website_user_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = wr.`assigned_to_user_id` ) LEFT JOIN ( SELECT `website_reach_comment_id`, `website_reach_id`, `user_id` FROM `website_reach_comments` ORDER BY `website_reach_comment_id` DESC ) AS wrc ON ( wrc.`website_reach_id` = wr.`website_reach_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = wr.`website_id` ) WHERE 1" . $where . " GROUP BY wr.`website_reach_id` $order_by, wrc.`website_reach_comment_id` DESC LIMIT $limit"
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
