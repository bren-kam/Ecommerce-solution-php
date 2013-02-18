<?php
class WebsiteTopBrand extends ActiveRecordBase {
    // The columns we will have access to
    public $website_id, $brand_id, $sequence;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_top_brands' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'sequence' => $this->sequence
        ), 'iii' );
    }

    /**
     * Get Top Brands
     *
     * @param int $account_id
     * @return Brand[]
     */
    public function get_all( $account_id ) {
        return $this->prepare(
            "SELECT b.* FROM `brands` AS b LEFT JOIN `website_top_brands` AS wtb ON ( wtb.`brand_id` = b.`brand_id` ) WHERE wtb.`website_id` = :account_id ORDER BY wtb.`sequence` ASC"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'Brand' );
    }

    /**
     * Update the sequence of many brands
     *
     * @param int $account_id
     * @param array $brands
     */
    public function update_sequence( $account_id, array $brands ) {
        // Starting with 0 for a sequence
        $sequence = 0;

        // Prepare statement
        $statement = $this->prepare_raw( 'UPDATE `website_top_brands` SET `sequence` = :sequence WHERE `brand_id` = :brand_id AND `website_id` = :account_id' );
        $statement->bind_param( ':sequence', $sequence, 'i' )
            ->bind_param( ':brand_id', $brand_id, 'i' )
            ->bind_value( ':account_id', $account_id, 'i' );

        // Loop through the statement and update anything as it needs to be updated
        foreach ( $brands as $brand_id ) {
            $statement->query();

            $sequence++;
        }
    }

    /**
     * Remove
     *
     * @param int $account_id
     * @param int $brand_id
     */
    public function remove( $account_id, $brand_id ) {
        $this->prepare(
            "DELETE FROM `website_top_brands` WHERE `brand_id` = :brand_id AND `website_id` = :account_id"
            , 'ii'
            , array( ':brand_id' => $brand_id, ':account_id' => $account_id )
        )->query();
    }
}
