<?php
class WebsiteCoupon extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_coupon_id, $website_id, $name, $code, $type, $amount, $minimum_purchase_amount
        , $store_wide, $buy_one_get_one_free, $item_limit, $date_start, $date_end, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_coupons' );

        // We want to make sure they match
        if ( isset( $this->website_coupon_id ) )
            $this->id = $this->website_coupon_id;
    }

    /**
     * Get by product
     *
     * @param int $account_id
     * @param int $product_id
     * @return array
     */
    public function get_by_product( $account_id, $product_id ) {
        $coupons = $this->prepare(
            'SELECT wc.`website_coupon_id`, wc.`name` FROM `website_coupons` AS wc LEFT JOIN `website_coupon_relations` AS wcr ON ( wcr.`website_coupon_id` = wc.`website_coupon_id` ) WHERE wc.`website_id` = :account_id AND wcr.`product_id` = :product_id'
            , 'ii'
            , array( ':account_id' => $account_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_ASSOC );

        if ( !empty( $coupons ) )
            $coupons = ar::assign_key( $coupons, 'website_coupon_id', true );

        return $coupons;
    }

    /**
     * Get by account
     *
     * @param int $account_id
     * @return WebsiteCoupon[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `website_coupon_id`, `name`, `code`, `type`, `amount`, `store_wide`, `item_limit`, `date_start`, `date_end`, `date_created` FROM `website_coupons` WHERE `store_wide` = 0 AND `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteCoupon' );
    }



    /**
     * Add Relations
     *
     * @param int $product_id
     * @param array $website_coupon_ids
     */
    public function add_relations( $product_id, array $website_coupon_ids ) {
        // Type Juggling
        $product_id = (int) $product_id;

        // Setup initial value
        $coupon_count = count( $website_coupon_ids );

        $coupon_values = substr( str_repeat( ", ( ?, $product_id )", $coupon_count ), 2 );

        $this->prepare(
            'INSERT INTO `website_coupon_relations` ( `website_coupon_id`, `product_id` ) VALUES ' . $coupon_values
            , str_repeat( 'i', $coupon_count )
            , $website_coupon_ids
        )->query();
    }

    /**
     * Delete by product
     *
     * @param int $account_id
     * @param int $product_id
     */
    public function delete_by_product( $account_id, $product_id ) {
        $this->prepare(
            'DELETE wcr.* FROM `website_coupon_relations` AS wcr LEFT JOIN `website_coupons` AS wc ON ( wc.`website_coupon_id` = wcr.`website_coupon_id` ) WHERE wcr.`product_id` = :product_id AND wc.`website_id` = :account_id'
            , 'ii'
            , array( ':product_id' => $product_id, ':account_id' => $account_id )
        )->query();
    }
}
