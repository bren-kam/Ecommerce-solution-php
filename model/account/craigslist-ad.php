<?php
class CraigslistAd extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $craigslist_ad_id, $website_id, $product_id, $text, $price, $error, $active, $date_posted
        , $date_created, $date_updated;

    // Fields from other tables
    public $headline, $product_name, $sku;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'craigslist_ads' );

        // We want to make sure they match
        if ( isset( $this->craigslist_ad_id ) )
            $this->id = $this->craigslist_ad_id;
    }

    /**
     * Get
     *
     * @param int $account_id
     * @param int $website_coupon_id
     */
    public function get( $account_id, $website_coupon_id ) {
        $this->prepare(
            'SELECT `website_coupon_id`, `website_id`, `name`, `code`, `type`, `amount`, `minimum_purchase_amount`, `store_wide`, `buy_one_get_one_free`, `item_limit`, DATE( `date_start` ) AS date_start, DATE( `date_end` ) AS date_end FROM `website_coupons` WHERE `website_id` = :account_id AND `website_coupon_id` = :website_coupon_id'
            , 'ii'
            , array( ':account_id' => $account_id, 'website_coupon_id' => $website_coupon_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_coupon_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => $this->name
            , 'code' => $this->code
            , 'type' => $this->type
            , 'amount' => $this->amount
            , 'minimum_purchase_amount' => $this->minimum_purchase_amount
            , 'store_wide' => $this->store_wide
            , 'buy_one_get_one_free' => $this->buy_one_get_one_free
            , 'item_limit' => $this->item_limit
            , 'date_start' => $this->date_start
            , 'date_end' => $this->date_end
            , 'date_created' => $this->date_created
        ), 'isssddiiisss' );

        $this->id = $this->website_coupon_id = $this->get_insert_id();
    }

    /**
     * Update
     */
    public function save() {
        $this->update( array(
            'name' => $this->name
            , 'code' => $this->code
            , 'type' => $this->type
            , 'amount' => $this->amount
            , 'minimum_purchase_amount' => $this->minimum_purchase_amount
            , 'store_wide' => $this->store_wide
            , 'buy_one_get_one_free' => $this->buy_one_get_one_free
            , 'item_limit' => $this->item_limit
            , 'date_start' => $this->date_start
            , 'date_end' => $this->date_end
        ), array(
            'website_coupon_id' => $this->website_coupon_id )
        , 'sssddiiiss', 'i' );
    }

    /**
     * List
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return CraigslistAd[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT ca.`craigslist_ad_id`, ca.`text`, cah.`headline`, p.`name` AS `product_name`, p.`sku`, ca.`date_created`, ca.`date_posted` FROM `craigslist_ads` AS ca LEFT JOIN `craigslist_ad_headlines` AS cah ON ( cah.`craigslist_ad_id` = ca.`craigslist_ad_id` ) LEFT JOIN `products` AS p ON( p.`product_id` = ca.`product_id` ) WHERE ca.`active` = 1 $where GROUP BY ca.`craigslist_ad_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'CraigslistAd' );
    }

    /**
     * Count all
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT ca.`craigslist_ad_id` ) FROM `craigslist_ads` AS ca LEFT JOIN `craigslist_ad_headlines` AS cah ON ( cah.`craigslist_ad_id` = ca.`craigslist_ad_id` ) LEFT JOIN `products` AS p ON( p.`product_id` = ca.`product_id` ) WHERE ca.`active` = 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
