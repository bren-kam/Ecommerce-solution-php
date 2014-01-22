<?php
class AccountNote extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_note_id, $website_id, $user_id, $message, $date_created;

    // Columns that belong to another table
    public $email, $contact_name, $store_name, $title;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_notes' );

        // We want to make sure they match
        if ( isset( $this->website_note_id ) )
            $this->id = $this->website_note_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'user_id' => $this->user_id
            , 'message' => strip_tags($this->message)
            , 'date_created' => $this->date_created
        ), 'iiss' );

        $this->website_note_id = $this->id = $this->get_insert_id();
    }

    /**
     * Get Note
     *
     * @param int $account_note_id
     */
    public function get( $account_note_id ) {
        $this->prepare(
            'SELECT `website_note_id`, `user_id`, `message` FROM `website_notes` WHERE `website_note_id` = :account_note_id'
            , 'i'
            , array( ':account_note_id' => $account_note_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_note_id;
    }

    /**
     * Get all account notes
     *
     * @param int $account_id
     * @return AccountNote[]
     */
    public function get_all( $account_id ) {
        // Get the account
		return $this->prepare(
            "SELECT a.`website_note_id`, a.`user_id`, a.`message`, a.`date_created`, b.`email`, b.`contact_name`, b.`store_name`, c.`title` FROM `website_notes` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) WHERE a.`website_id` = :account_id ORDER BY date_created DESC"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountNote' );
    }

    /**
     * Delete note
     */
    public function delete() {
        parent::delete( array( 'website_note_id' => $this->id ), 'i' );
    }
}
