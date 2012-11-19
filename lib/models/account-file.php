<?php
class AccountFile extends ActiveRecordBase {
    public $id, $website_file_id, $website_id, $file_path, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_files' );

        // We want to make sure they match
        if ( isset( $this->website_file_id ) )
            $this->id = $this->website_file_id;
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @return array
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT * FROM `website_files` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountFile' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'file_path' => $this->file_path
            , 'date_created' => $this->date_created
        ), 'iss' );

        $this->id = $this->website_file_id = $this->get_insert_id();
    }
}
