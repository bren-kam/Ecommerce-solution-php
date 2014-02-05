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
     * Get
     *
     * @param int $website_coupon_id
     * @param int $account_id
     */
    public function get( $website_coupon_id, $account_id ) {
        $this->prepare(
            'SELECT `website_coupon_id`, `website_id`, `name`, `code`, `type`, `amount`, `minimum_purchase_amount`, `store_wide`, `buy_one_get_one_free`, `item_limit`, DATE( `date_start` ) AS date_start, DATE( `date_end` ) AS date_end FROM `website_coupons` WHERE `website_id` = :account_id AND `website_coupon_id` = :website_coupon_id'
            , 'ii'
            , array( ':account_id' => $account_id, 'website_coupon_id' => $website_coupon_id )
        )->get_row( PDO::FETCH_INTO, $this );

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
     * Get Free Shipping Methods
     *
     * @return array
     */
    public function get_free_shipping_methods() {
        return $this->prepare(
            'SELECT `website_shipping_method_id` FROM `website_coupon_shipping_methods` WHERE `website_coupon_id` = :website_coupon_id ORDER BY `website_coupon_id` ASC'
            , 'i'
            , array( ':website_coupon_id' => $this->id )
        )->get_col();
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => strip_tags($this->name)
            , 'code' => strip_tags($this->code)
            , 'type' => strip_tags($this->type)
            , 'amount' => $this->amount
            , 'minimum_purchase_amount' => $this->minimum_purchase_amount
            , 'store_wide' => $this->store_wide
            , 'buy_one_get_one_free' => $this->buy_one_get_one_free
            , 'item_limit' => $this->item_limit
            , 'date_start' => strip_tags($this->date_start)
            , 'date_end' => strip_tags($this->date_end)
            , 'date_created' => $this->date_created
        ), 'isssddiiisss' );

        $this->id = $this->website_coupon_id = $this->get_insert_id();
    }

    /**
     * Update
     */
    public function save() {
        $this->update( array(
            'name' => strip_tags($this->name)
            , 'code' => strip_tags($this->code)
            , 'type' => strip_tags($this->type)
            , 'amount' => $this->amount
            , 'minimum_purchase_amount' => $this->minimum_purchase_amount
            , 'store_wide' => $this->store_wide
            , 'buy_one_get_one_free' => $this->buy_one_get_one_free
            , 'item_limit' => $this->item_limit
            , 'date_start' => strip_tags($this->date_start)
            , 'date_end' => strip_tags($this->date_end)
        ), array(
            'website_coupon_id' => $this->id )
        , 'sssddiiiss', 'i' );
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
     * Add Free Shipping Methods
     *
     * @param array $shipping_methods
     * @return array
     */
    public function add_free_shipping_methods( array $shipping_methods ) {
        // Create values
        $values = '';

        foreach ( $shipping_methods as $website_shipping_method_id ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= '( ' . (int) $this->id . ', ' . (int) $website_shipping_method_id . ' )';
        }

        // Create new free shipping methods
        $this->query( "INSERT INTO `website_coupon_shipping_methods` ( `website_coupon_id`, `website_shipping_method_id` ) VALUES $values" );
    }

    /**
     * Delete
     */
    public function remove() {
        $this->delete( array(
            'website_coupon_id' => $this->id
            , 'website_id' => $this->website_id
        ), 'ii' );
    }

    /**
     * Delete relations by product
     *
     * @param int $account_id
     * @param int $product_id
     */
    public function delete_relations_by_product( $account_id, $product_id ) {
        $this->prepare(
            'DELETE wcr.* FROM `website_coupon_relations` AS wcr LEFT JOIN `website_coupons` AS wc ON ( wc.`website_coupon_id` = wcr.`website_coupon_id` ) WHERE wcr.`product_id` = :product_id AND wc.`website_id` = :account_id'
            , 'ii'
            , array( ':product_id' => $product_id, ':account_id' => $account_id )
        )->query();
    }

    /**
     * Delete Free Shipping Methods
     *
     * @return array
     */
    public function delete_free_shipping_methods() {
        $this->prepare(
            'DELETE FROM `website_coupon_shipping_methods` WHERE `website_coupon_id` = :website_coupon_id'
            , 'i'
            , array( ':website_coupon_id' => $this->id )
        )->query();
    }

    /**
     * List
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return WebsiteCoupon[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `website_coupon_id`, `name`, `type`, `amount`, `item_limit`, `date_created` FROM `website_coupons` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteCoupon' );
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
            "SELECT COUNT( `website_coupon_id` )FROM `website_coupons` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
