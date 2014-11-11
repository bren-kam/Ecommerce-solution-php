<?php
class AccountBrandCategory extends ActiveRecordBase {
    public $website_id, $brand_id, $category_id, $image_url, $date_updated;

    // Fields from other tables
    public $slug;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_brand_category' );
    }

    /**
     * Get
     *
     * @param int $account_id
     * @param int $brand_id
     * @param int $category_id
     * @return AccountBrandCategory
     */
    public function get( $account_id, $brand_id, $category_id ) {
        $this->prepare(
            "SELECT * FROM website_brand_category wbc WHERE wbc.`brand_id` = :brand_id AND wbc.`category_id` = :category_id AND wbc.`website_id` = :account_id"
            , 'ii'
            , array( ':account_id' => $account_id, ':brand_id' => $brand_id, ':category_id' => $category_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'category_id' => $this->category_id
            , 'image_url' => $this->image_url
        ), 'iiis');
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'category_id' => $this->category_id
            , 'image_url' => $this->image_url
        ), array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'category_id' => $this->category_id
        ), 'iiis', 'iii' );
    }

}
