<?php
class EmailList extends ActiveRecordBase {
    public $id, $email_list_id, $category_id, $website_id, $name, $date_created;

    // Artifical fields
    public $count;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_lists' );

        // We want to make sure they match
        if ( isset( $this->email_list_id ) )
            $this->id = $this->email_list_id;
    }

    /**
     * Get Default Email List
     *
     * @param int $account_id
     */
    public function get_default_email_list( $account_id ) {
        $this->prepare(
            'SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = :account_id AND `category_id` = 0'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
		
		$this->id = $this->email_list_id;
    }

    /**
     * Get Email lists by account
     *
     * @param int $account_id
     * @return EmailList[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_list_id`, `name` FROM `email_lists` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Get Email lists by account
     *
     * @param int $account_id
     * @return EmailList[]
     */
    public function get_count_by_account( $account_id ) {
        return $this->prepare(
            'SELECT el.`email_list_id`, el.`category_id`, el.`name`, COUNT( DISTINCT ea.`email_id` ) AS count FROM `email_lists` AS el LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` ) WHERE el.`website_id` = :account_id AND e.`status` = 1 GROUP BY el.`email_list_id` ORDER BY el.`name`'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Get Email lists by account
     *
     * @param int $email_message_id
     * @param int $account_id
     * @return EmailList[]
     */
    public function get_by_message( $email_message_id, $account_id ) {
        return $this->prepare(
            'SELECT el.`email_list_id`, el.`name` FROM `email_lists` LEFT JOIN `email_message_associations` AS ema ON ( ema.`email_list_id` = el.`email_list_id` ) LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` ) WHERE el.`website_id` = :account_id AND ema.`email_message_id` = :email_message_id GROUP BY el.`email_list_id`'
            , 'i'
            , array( ':email_message_id' => $email_message_id, ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Create Email List
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => $this->name
            , 'date_created' => $this->date_created
        ), 'iss' );

        $this->id = $this->email_list_id = $this->get_insert_id();
    }
}
