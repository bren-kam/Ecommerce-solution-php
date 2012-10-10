<?php
class Email extends ActiveRecordBase {
    public $id, $email_id, $email, $name, $phone, $status, $date_created, $date_unsubscribed, $date_synced, $timestamp;

    // Fields available from other tables
    public $website_id, $mc_list_id;

    // Artificial field
    public $interests;

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
     * Get Unsynced Emails
     *
     * @return array
     */
    public function get_unsynced() {
        return $this->get_results( "SELECT e.`email`, e.`email_id`, e.`name`, GROUP_CONCAT( el.`name` ) AS interests, w.`mc_list_id`, w.`website_id` FROM `emails` AS e INNER JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) INNER JOIN `email_lists` AS el ON ( el.`email_list_id` = ea.`email_list_id` ) INNER JOIN `websites` AS w ON ( w.`website_id` = el.`website_id` ) WHERE e.`status` = 1 AND ( e.`date_synced` = '0000-00-00 00:00:00' OR e.`timestamp` > e.`date_synced` ) AND w.`email_marketing` = 1 GROUP BY el.`website_id`, e.`email`", PDO::FETCH_CLASS, 'Email' );
    }
}
