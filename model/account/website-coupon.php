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
}
