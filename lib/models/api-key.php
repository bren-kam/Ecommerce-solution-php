<?php
class APIKey extends ActiveRecordBase {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // The columns we will have access to
    public $id, $api_key_id, $company_id, $brand_id, $user_id, $key, $status, $date_created;

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
            'SELECT ak.`api_key_id`, ak.`company_id`, ak.`brand_id`, ak.`user_id`, ak.`key`, c.`domain` FROM `api_keys` AS ak LEFT JOIN `companies` AS c ON ( c.`company_id` = ak.`company_id` ) WHERE ak.`status` = :status AND ak.`key` = :key'
            , 'is'
            , array( ':status' => self::STATUS_ACTIVE, ':key' => $key )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->api_key_id;
    }
}