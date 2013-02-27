<?php
class Email extends ActiveRecordBase {
    public $id, $email_id, $website_id, $email, $name, $phone, $status, $date_created, $date_unsubscribed, $date_synced, $timestamp;

    // Fields available from other tables
    public $mc_list_id;

    // Artificial field
    public $interests, $date;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'emails' );

        // We want to make sure they match
        if ( isset( $this->email_id ) )
            $this->id = $this->email_id;
    }

    /**
     * Get Email by Email
     *
     * @param int $account_id
     * @param string $email
     * @return Email
     */
    public function get_by_email( $account_id, $email ) {
		return $this->prepare( 'SELECT `email_id` FROM `emails` WHERE `website_id` = :account_id AND `email` = :email'
            , 'is'
            , array(
                ':account_id' => $account_id
                , ':email' => $email
            )
        )->get_row( PDO::FETCH_CLASS, 'Email' );
    }

    /**
     * Get Dashboard Subscribers By Account
     *
     * @param int $account_id
     * @return Email[]
     */
    public function get_dashboard_subscribers_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_id`, `email` FROM `emails` WHERE `website_id` = :account_id AND `status` = 1 ORDER BY `date_created` DESC LIMIT 5'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'Email' );
    }

    /**
     * Get Unsynced Emails
     *
     * @return Email[]
     */
    public function get_unsynced() {
        return $this->get_results(
            "SELECT e.`email`, e.`email_id`, e.`name`, GROUP_CONCAT( el.`name` ) AS interests, w.`mc_list_id`, w.`website_id` FROM `emails` AS e INNER JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) INNER JOIN `email_lists` AS el ON ( el.`email_list_id` = ea.`email_list_id` ) INNER JOIN `websites` AS w ON ( w.`website_id` = el.`website_id` ) WHERE e.`status` = 1 AND ( e.`date_synced` = '0000-00-00 00:00:00' OR e.`timestamp` > e.`date_synced` ) AND w.`email_marketing` = 1 GROUP BY el.`website_id`, e.`email`"
            , PDO::FETCH_CLASS
            , 'Email'
        );
    }

    /**
     * Get Unsynced Emails by account
     *
     * @param int $account_id
     * @return Email[]
     */
    public function get_unsynced_by_account( $account_id ) {
        return $this->prepare(
            "SELECT e.`email`, e.`email_id`, e.`name`, GROUP_CONCAT( el.`name` ) AS interests, w.`mc_list_id`, w.`website_id` FROM `emails` AS e INNER JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) INNER JOIN `email_lists` AS el ON ( el.`email_list_id` = ea.`email_list_id` ) INNER JOIN `websites` AS w ON ( w.`website_id` = el.`website_id` ) WHERE e.`status` = 1 AND ( e.`date_synced` = '0000-00-00 00:00:00' OR e.`timestamp` > e.`date_synced` ) AND w.`website_id` = :account_id AND w.`email_marketing` = 1 GROUP BY el.`website_id`, e.`email`"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'Email' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert(
            array(
                'website_id' => $this->website_id
                , 'email' => $this->email
                , 'name' => $this->name
                , 'status' => $this->status
                , 'date_created' => $this->date_created
            ), 'issis'
        );

        $this->email_id = $this->id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        parent::update(
            array( 'status' => $this->status )
            , array( 'email_id' => $this->id )
            , 'i', 'i'
        );
    }

    /**
     * Unsubscribe Bulk
     *
     * @param array $emails
     * @param int $account_id
     */
    public function unsubscribe_bulk( array $emails, $account_id ) {
        if ( empty( $emails ) )
            return;

        //Type Juggling
        $account_id = (int) $account_id;
        $email_count = count( $emails );

        $this->prepare(
            "UPDATE `emails` SET `status` = 0 WHERE `website_id` = $account_id AND `email` IN (" . substr( str_repeat( ',?', $email_count ), 1 ) . ')'
            , str_repeat( 's', $email_count )
            , $emails
        )->query();
    }

    /**
     * Clean Bulk
     *
     * @param array $emails
     * @param int $account_id
     */
    public function clean_bulk( array $emails, $account_id ) {
        if ( empty( $emails ) )
            return;

        //Type Juggling
        $account_id = (int) $account_id;
        $email_count = count( $emails );

        $this->prepare(
            "UPDATE `emails` SET `status` = 2 WHERE `website_id` = $account_id AND `email` IN (" . substr( str_repeat( ',?', $email_count ), 1 ) . ')'
            , str_repeat( 's', $email_count )
            , $emails
        )->query();
    }

    /**
     * Sync Bulk
     *
     * @param array $email_ids
     */
    public function sync_bulk( $email_ids ) {
        foreach ( $email_ids as &$id ) {
            $id = (int) $id;
        }

        // Update emails to make them synced
        $this->query( 'UPDATE `emails` SET `date_synced` = NOW() WHERE `email_id` IN (' . implode( ',', $email_ids ) . ')' );
    }

    /**
     * List Subscribers
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailMessage[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT DISTINCT `email_id`, `name`, `email`, `phone`, IF( 1 = `status`, `date_created`, `timestamp` ) AS date FROM `emails` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * Count all the pages
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT `email_id` ) FROM `emails` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }

    /***** ASSOCIATIONS *****/

    /**
     * Add Associations
     *
     * @param array $email_list_ids
     */
    public function add_associations( array $email_list_ids ) {
        if ( empty( $email_list_ids ) )
            return;

        $email_id = (int) $this->id;
        $values = array();

        foreach ( $email_list_ids as $elid ) {
            $elid = (int) $elid;

            $values[] = "( $email_id, $elid )";
        }

        $this->query( "INSERT INTO `email_associations` VALUES " . implode( ',', $values ) . ' ON DUPLICATE KEY UPDATE `email_list_id` = VALUES( `email_list_id` )' );
    }
}
