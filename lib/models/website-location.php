<?php
class WebsiteLocation extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $name, $address, $city, $state, $zip, $phone, $fax, $email, $website, $store_hours
        , $sequence, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_location' );
    }

    /**
     * Get by website
     *
     * @param int$website_id
     * @return WebsiteLocation[]
     */
    public function get_by_website( $website_id ) {
        return $this->prepare(
            'SELECT * FROM `website_location` WHERE `website_id` = :website_id'
            , 'i'
            , array( ':website_id' => $website_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteLocation' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->id = $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => $this->name
            , 'address' => $this->address
            , 'city' => $this->city
            , 'state' => $this->state
            , 'zip' => $this->zip
            , 'phone' => $this->phone
            , 'fax' => $this->fax
            , 'email' => $this->email
            , 'website' => $this->website
            , 'store_hours' => $this->store_hours
            , 'sequence' => $this->sequence
            , 'date_created' => $this->date_created
        ), 'issssssssssis' );
   }
}
