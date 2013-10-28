<?php
class WebsiteAutoPrice extends ActiveRecordBase {
    // The columns we will have access to
    public $website_id, $category_id, $price, $sale_price, $alternate_price, $ending, $future;

    /**
     * @var WebsiteAutoPrice[]
     */
    public static $auto_prices;

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
            , 'category_id' => $this->category_id
        ), 'ii' );
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
            , 'category_id' => $this->category_id
        ), 'ddddi', 'ii' );
    }

    /**
     * Get by website
     *
     * @param int $website_id
     * @return WebsiteAutoPrice[]
     */
    public function load_all( $website_id ) {
        WebsiteAutoPrice::$auto_prices = null;

        $results = $this->prepare(
            'SELECT * FROM `website_auto_price` WHERE `website_id` = :website_id'
            , 'i'
            , array( ':website_id' => $website_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteAutoPrice' );

        foreach ( $results as $auto_price ) {
            WebsiteAutoPrice::$auto_prices[$auto_price->category_id] = $auto_price;
        }

        return WebsiteAutoPrice::$auto_prices;
    }

    /**
     * Get based on category
     *
     * @param int $website_id
     * @param int $category_id
     */
    public function get_by_category( $website_id, $category_id ) {
        if ( isset( WebsiteAutoPrice::$auto_prices[$category_id] ) ) {
            $this->import( WebsiteAutoPrice::$auto_prices[$category_id] );
        } else {
            $this->website_id = $website_id;
            $this->category_id = $category_id;
            $this->create();
        }
    }

    /**
     * Imports another website auto price
     *
     * @param WebsiteAutoPrice $website_auto_price
     */
    public function import( WebsiteAutoPrice $website_auto_price ) {
        foreach ( get_object_vars($website_auto_price) as $key => $value ) {
            $this->$key = $value;
        }
    }
}
