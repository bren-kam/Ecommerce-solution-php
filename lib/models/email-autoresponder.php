<?php
class EmailAutoresponder extends ActiveRecordBase {
    public $id, $email_autoresponder_id, $website_id, $email_list_id, $name, $subject, $message, $current_offer, $default, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_autoresponders' );

         // We want to make sure they match
        if ( isset( $this->email_autoresponder_id ) )
            $this->id = $this->email_autoresponder_id;
    }

    /**
     * Get
     *
     * @param int $email_autoresponder_id
     * @param int $account_id
     */
    public function get( $email_autoresponder_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `email_autoresponders` WHERE `email_autoresponder_id` = :email_autoresponder_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':email_autoresponder_id' => $email_autoresponder_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->email_autoresponder_id;
    }

    /**
     * Create Auto
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'email_list_id' => $this->email_list_id
            , 'name' => strip_tags($this->name)
            , 'subject' => strip_tags($this->subject)
            , 'message' => format::strip_only( $this->message, '<script>' )
            , 'current_offer' => $this->current_offer
            , 'default' => $this->default
            , 'date_created' => $this->date_created
        ), 'iisssiis' );

        $this->id = $this->email_autoresponder_id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'email_list_id' => $this->email_list_id
            , 'name' => strip_tags($this->name)
            , 'subject' => strip_tags($this->subject)
            , 'message' => format::strip_only( $this->message, '<script>' )
            , 'current_offer' => $this->current_offer
        ), array(
            'email_autoresponder_id' => $this->email_autoresponder_id
        ), 'isssi', 'i' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'email_autoresponder_id' => $this->email_autoresponder_id
        ), 'i' );
    }

    /**
     * List All
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailAutoresponder[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `email_autoresponder_id`, `name`, `subject`, `default` FROM `email_autoresponders` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailAutoresponder' );
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
            "SELECT COUNT( `email_autoresponder_id` ) FROM `email_autoresponders` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
