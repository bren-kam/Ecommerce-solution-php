<?php
class EmailList extends ActiveRecordBase {
    public $id, $email_list_id, $category_id, $website_id, $ac_list_id, $name, $description, $date_created;

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
     * Get Email List
     *
     * @param int $email_list_id
     * @param int $account_id
     */
    public function get( $email_list_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `email_lists` WHERE `email_list_id` = :email_list_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':email_list_id' => $email_list_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
		
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
            'SELECT `email_list_id`, `category_id`, `ac_list_id`, `name` FROM `email_lists` WHERE `website_id` = :account_id'
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
            'SELECT el.`email_list_id`, el.`name` FROM `email_lists` AS el LEFT JOIN `email_message_associations` AS ema ON ( ema.`email_list_id` = el.`email_list_id` ) LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` ) WHERE el.`website_id` = :account_id AND ema.`email_message_id` = :email_message_id GROUP BY el.`email_list_id`'
            , 'ii'
            , array( ':email_message_id' => $email_message_id, ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Get AC List IDs
     *
     * @param array $email_list_ids
     * @param int $account_id
     * @return array
     */
    public function get_ac_list_ids( array $email_list_ids, $account_id ) {
        foreach( $email_list_ids as &$email_list_id ) {
            $email_list_id = (int) $email_list_id;
        }

        return $this->prepare(
            'SELECT `ac_list_id` FROM `email_lists` WHERE `website_id` = :account_id AND `email_list_id` IN (' . implode( ',', $email_list_ids ) . ')'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    /**
     * Create Email List
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'ac_list_id' => $this->ac_list_id
            , 'name' => $this->name
            , 'description' => $this->description
            , 'date_created' => $this->date_created
        ), 'iisss' );

        $this->id = $this->email_list_id = $this->get_insert_id();
    }

    /**
     * Save Email List
     */
    public function save() {
        $this->update( array(
            'ac_list_id' => $this->ac_list_id
            , 'name' => $this->name
            , 'description' => $this->description
        ), array(
            'email_list_id' => $this->id
        ), 'iss', 'i' );
    }
    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'email_list_id' => $this->email_list_id
        ), 'i' );
    }

    /**
     * List All
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailList[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT el.`email_list_id`, el.`name`, el.`description`, el.`date_created`, IF( 1 = e.`status`, COUNT( DISTINCT ea.`email_id` ), 0 ) AS count FROM `email_lists` AS el LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` AND e.`status` = 1 ) WHERE 1 $where GROUP BY el.`email_list_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
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
            "SELECT COUNT( DISTINCT el.`email_list_id` ) FROM `email_lists` AS el LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` AND e.`status` = 1 ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
