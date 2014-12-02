<?php
class APIKey extends ActiveRecordBase {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // The columns we will have access to
    public $id, $api_key_id, $company_id, $brand_id, $user_id, $key, $status, $date_created;

    // Columns belong to other tables
    public $company, $domain;

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
            'SELECT ak.`api_key_id`, ak.`company_id`, ak.`brand_id`, ak.`user_id`, ak.`key`, c.`name` as `company`, c.`domain` FROM `api_keys` AS ak LEFT JOIN `companies` AS c ON ( c.`company_id` = ak.`company_id` ) WHERE ak.`status` = :status AND ak.`key` = :key'
            , 'is'
            , array( ':status' => self::STATUS_ACTIVE, ':key' => $key )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->api_key_id;
    }

    /**
     * Get
     * @param $id
     */
    public function get( $id ) {
        $this->prepare(
            'SELECT ak.`api_key_id`, ak.`company_id`, ak.`brand_id`, ak.`user_id`, ak.`key`, c.`name` as `company`, c.`domain`, ak.`status` FROM `api_keys` AS ak LEFT JOIN `companies` AS c ON ( c.`company_id` = ak.`company_id` ) WHERE ak.`api_key_id` = :id'
            , 'is'
            , array( ':id' => $id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->api_key_id;
    }

    /**
     * List All
     * @param $variables
     * @return APIKey[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        $where = $where ? "WHERE 1 $where" : '';

        return $this->prepare( "SELECT a.`api_key_id`, a.`company_id`, a.`brand_id`, a.`user_id`, a.`key`, a.`status`, c.`name` as `company`, c.`domain` FROM `api_keys` AS a LEFT JOIN `companies` AS c ON ( c.`company_id` = a.`company_id` ) $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'APIKey' );
    }

    /**
     * Count All
     * @param $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        $where = $where ? "WHERE 1 $where" : '';

        return $this->prepare( "SELECT COUNT(DISTINCT `api_key_id`) FROM api_keys `a` LEFT JOIN `companies` AS c ON ( c.`company_id` = a.`company_id` ) $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }

    /**
     * Get Brands IDs
     * @return array
     */
    public function get_brand_ids() {
        return $this->prepare(
            'SELECT brand_id FROM api_key_brand WHERE api_key_id = :id'
            , 'i'
            , array( ':id' => $this->id )
        )->get_col();
    }

    /**
     * Get Ashley Accounts
     * @return array
     */
    public function get_ashley_accounts() {
        return $this->prepare(
            'SELECT ashley_account FROM api_key_ashley_account WHERE api_key_id = :id'
            , 'i'
            , array( ':id' => $this->id )
        )->get_col();
    }

    /**
     * Set Brands
     * @param array $brand_ids
     */
    public function set_brands( $brand_ids = array() ) {
        $this->prepare(
            'DELETE FROM api_key_brand WHERE api_key_id = :id'
            , 'i'
            , array(':id' => $this->id )
        )->query();

        if ( !$brand_ids )
            return;

        $values = array();
        foreach ( $brand_ids as $brand_id ) {
            $values[] = "({$this->id}, {$brand_id})";
        }
        $values_str = implode( ',', $values );

        $this->prepare(
            'INSERT INTO api_key_brand(api_key_id, brand_id) VALUES ' . $values_str
            , ''
            , array()
        )->query();
    }

    /**
     * Set Ashley Accounts
     * @param array $ashley_accounts
     */
    public function set_ashley_accounts( $ashley_accounts = array() ) {
        $this->prepare(
            'DELETE FROM api_key_ashley_account WHERE api_key_id = :id'
            , 'i'
            , array(':id' => $this->id )
        )->query();

        if ( !$ashley_accounts )
            return;

        $values = array();
        foreach ( $ashley_accounts as $ashley_account ) {
            $values[] = "({$this->id}, '{$ashley_account}')";
        }
        $values_str = implode( ',', $values );

        $this->prepare(
            'INSERT INTO api_key_ashley_account(api_key_id, ashley_account) VALUES ' . $values_str
            , ''
            , array()
        )->query();
    }

    /**
     * Create
     */
    public function create() {
        $this->insert(array(
            'company_id' => $this->company_id
            , 'key' => $this->key
            , 'status' => $this->status
        ), 'isi');
        $this->id = $this->api_key_id = $this->get_insert_id();
    }

    public function save() {
        $this->update(
            array(
                'company_id' => $this->company_id
                , 'status' => $this->status
            )
            , array( 'api_key_id' => $this->id )
            ,'is'
            , 'i'
        );
    }

}