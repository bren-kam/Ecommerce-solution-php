<?php
class EmailList extends ActiveRecordBase {
    public $id, $email_list_id, $website_id, $name, $date_created;

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
     * @return EmailList
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
