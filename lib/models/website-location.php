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
     * Get
     *
     * @param int $id
     * @param int $website_id
     */
    public function get( $id, $website_id ) {
        $this->prepare(
            'SELECT * FROM `website_location` WHERE `id` = :id AND `website_id` = :website_id'
            , 'ii'
            , array( ':id' => $id, ':website_id' => $website_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get by website
     *
     * @param int $website_id
     * @return WebsiteLocation[]
     */
    public function get_by_website( $website_id ) {
        return $this->prepare(
            'SELECT * FROM `website_location` WHERE `website_id` = :website_id ORDER BY `sequence` ASC'
            , 'i'
            , array( ':website_id' => $website_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteLocation' );
    }

    /**
     * Count
     *
     * @param int $website_id
     * @return int
     */
    public function count( $website_id ) {
        return $this->prepare(
            'SELECT COUNT(*) FROM `website_location` WHERE `website_id` = :website_id ORDER BY `sequence` ASC'
            , 'i'
            , array( ':website_id' => $website_id )
        )->get_var();
    }

    /**
     * Create
     */
    public function create() {
        // Set a couple of other variables
        $this->sequence = $this->count( $this->website_id );
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

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'name' => $this->name
            , 'address' => $this->address
            , 'city' => $this->city
            , 'state' => $this->state
            , 'zip' => $this->zip
            , 'phone' => $this->phone
            , 'fax' => $this->fax
            , 'email' => $this->email
            , 'website' => $this->website
            , 'store_hours' => $this->store_hours
        ), array(
            'id' => $this->id
        ), 'issssssssss', 'i' );
   }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'id' => $this->id
            , 'website_id' => $this->website_id
        ), 'ii' );
    }

    /**
     * Update the sequence of many locations
     *
     * @param int $account_id
     * @param array $locations
     */
    public function update_sequence( $account_id, array $locations ) {
        // Starting with 0 for a sequence
        $sequence = 0;

        // Prepare statement
        $statement = $this->prepare_raw( 'UPDATE `website_location` SET `sequence` = :sequence WHERE `id` = :id AND `website_id` = :account_id' );
        $statement->bind_param( ':sequence', $sequence, 'i' )
            ->bind_param( ':id', $id, 'i' )
            ->bind_value( ':account_id', $account_id, 'i' );

        // Loop through the statement and update anything as it needs to be updated
        foreach ( $locations as $id ) {
            $statement->query();

            $sequence++;
        }
    }
}
