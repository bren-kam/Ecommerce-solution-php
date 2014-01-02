<?php
class WebsiteAutoPrice extends ActiveRecordBase {
    // The columns we will have access to
    public $website_id, $brand_id, $category_id, $price, $sale_price, $alternate_price, $ending, $future;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_auto_price' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'category_id' => $this->category_id
            , 'price' => $this->price
            , 'sale_price' => $this->sale_price
            , 'alternate_price' => $this->alternate_price
            , 'ending' => $this->ending
            , 'future' => $this->future
        ), 'iiiddddi' );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'price' => $this->price
            , 'sale_price' => $this->sale_price
            , 'alternate_price' => $this->alternate_price
            , 'ending' => $this->ending
            , 'future' => $this->future
        ), array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'category_id' => $this->category_id
        ), 'ddddi', 'iii' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'category_id' => $this->category_id
        ), 'iii' );
    }

    /**
     * Get
     *
     * @param int $brand_id
     * @param int $category_id
     * @param int $website_id
     */
    public function get( $brand_id, $category_id, $website_id ) {
        $this->prepare(
            'SELECT * FROM `website_auto_price` WHERE `website_id` = :website_id AND `brand_id` = :brand_id AND `category_id` = :category_id'
            , 'iii'
            , array( ':website_id' => $website_id, ':brand_id' => $brand_id, ':category_id' => $category_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get by website
     *
     * @param int $website_id
     * @return WebsiteAutoPrice[]
     */
    public function get_all( $website_id ) {
        return $this->prepare(
            'SELECT * FROM `website_auto_price` WHERE `website_id` = :website_id'
            , 'i'
            , array( ':website_id' => $website_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteAutoPrice' );
    }
}
