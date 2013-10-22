<?php
class MobileList extends ActiveRecordBase {
    public $id, $mobile_list_id, $website_id, $name, $frequency, $description, $date_created, $date_updated;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'mobile_lists' );

        // We want to make sure they match
        if ( isset( $this->mobile_list_id ) )
            $this->id = $this->mobile_list_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => strip_tags($this->name)
            , 'frequency' => $this->frequency
            , 'description' => strip_tags($this->description)
            , 'date_created' => $this->date_created
        ), 'isiss' );

        $this->id = $this->mobile_list_id = $this->get_insert_id();
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @return array
     */
    public function get_name_index_by_account( $account_id ) {
        return ar::assign_key( $this->prepare(
            "SELECT `mobile_list_id`, `name` FROM `mobile_lists` WHERE `website_id` = :account_id"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC ), 'name', true );
    }
}
