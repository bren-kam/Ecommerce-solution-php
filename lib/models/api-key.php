<?php
class APIKey extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $api_key_id, $company_id, $key, $status, $date_created;

    // Columns belong to other tables
    public $domain;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'api_keys' );

        // We want to make sure they match
        if ( isset( $this->api_key_id ) )
            $this->id = $this->api_key_id;
    }

    /**
     * Get By Key
     *
     * @param $key
     */
    public function get_by_key( $key ) {
        $this->prepare(
            'SELECT ak.`api_key_id`, ak.`company_id`, ak.`key`, c.`domain` FROM `api_keys` AS ak LEFT JOIN `companies` AS c ON ( c.`company_id` = ak.`company_id` ) WHERE ak.`status` = 1 AND ak.`key` = :key'
            , 's'
            , array( ':key' => $key )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->api_key_id;
    }
}