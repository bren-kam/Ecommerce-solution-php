<?php
class WebsiteCart extends ActiveRecordBase {

    // The columns we will have access to
    public $id, $website_cart_id, $website_id, $website_user_id, $website_shipping_method_id, $website_ashley_express_shipping_method_id
        , $website_coupon_id, $website_order_id, $shipping_price, $tax_price, $coupon_discount, $total_price, $email, $phone
        , $billing_first_name, $billing_last_name, $billing_address1, $billing_address2, $billing_city
        , $billing_state, $billing_zip, $billing_phone, $billing_alt_phone, $shipping_name, $shipping_first_name
        , $shipping_last_name, $shipping_address1, $shipping_address2, $shipping_city, $shipping_state
        , $shipping_zip, $status, $date_created, $shipping_track_number, $authorize_only, $timestamp;

    // Artificial field
    public $name, $products;

    // Belonging to another table
    public $shipping_method, $ashley_express_shipping_method;

    /**
     * @var WebsiteCartItem[]
     */
    public $items;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_carts' );

        // We want to make sure they match
        if ( isset( $this->website_cart_id ) )
            $this->id = $this->website_cart_id;
    }

    /**
     * Get
     * @param int $website_cart_id
     * @param int $account_id
     */
    public function get( $website_cart_id, $account_id ) {
        $this->prepare(
            "SELECT wc.*, IF( '' = wo.`shipping_name`, CONCAT( wo.`shipping_first_name`, ' ', wo.`shipping_last_name` ), wo.`shipping_name` ) AS shipping_name, wsm.`name` AS shipping_method, wsm_ashley_express.`name` AS ashley_express_shipping_method FROM `website_carts` AS wc LEFT JOIN `website_orders` wo ON (wo.website_cart_id = wc.website_cart_id) LEFT JOIN `website_shipping_methods` AS wsm ON ( wsm.`website_shipping_method_id` = wo.`website_shipping_method_id` ) LEFT JOIN `website_shipping_methods` AS wsm_ashley_express ON ( wsm_ashley_express.`website_shipping_method_id` = wo.`website_ashley_express_shipping_method_id` ) WHERE wc.`website_cart_id` = :website_cart_id AND wc.`website_id` = :account_id"
            , 'ii'
            , array( ':website_cart_id' => $website_cart_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_cart_id;
    }

    /**
     * Get
     *
     * @param int $website_cart_id
     * @param int $account_id
     */
    public function get_complete( $website_cart_id, $account_id, WebsiteCartItem $website_cart_item = null ) {
        // Get the main order
        $this->get( $website_cart_id, $account_id );

        // Get items
        if ( is_null( $website_cart_item ) )
            $website_cart_item = new WebsiteCartItem();

        $this->items = $website_cart_item->get_all( $this->id );
    }

    /**
     * Save
     */
    public function save() {
        parent::update( array(
            'status' => $this->status
        ), array(
            'website_cart_id' => $this->id
        ), 'i', 'i' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'website_cart_id' => $this->id
        ), 'i' );
    }

    /**
	 * List Website Orders
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return WebsiteOrder[]
	 */
	public function list_all( $variables ) {
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT wc.`website_cart_id`, wc.`website_id`, wc.`name`, wc.`total_price`, wo.`website_order_id`, wc.`date_created`, GROUP_CONCAT(p.`name`) products, wc.timestamp
              FROM `website_carts` wc
              LEFT JOIN `website_orders` wo ON ( wo.website_cart_id = wc.website_cart_id )
              INNER JOIN `website_cart_items` wci ON ( wc.website_cart_id = wci.website_cart_id )
              INNER JOIN `products` p ON ( p.product_id = wci.product_id )
              WHERE 1 $where
              GROUP BY wc.website_cart_id
              $order_by
              LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteCart' );
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
        return $this->prepare( "SELECT DISTINCT COUNT( `website_cart_id` )  FROM `website_carts` wc WHERE 1 $where GROUP BY `website_cart_id`"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
     * Get Remarketing Report
     * @param $website_id
     * @param $since
     * @return array
     */
    public function get_remarketing_report($website_id, DateTime $since) {
        return $this->prepare(
            "SELECT
                SUM( CASE WHEN wo.website_cart_id IS NULL THEN wc.total_price ELSE 0 END ) as abandoned_amount,
                SUM( CASE WHEN wo.website_cart_id IS NOT NULL THEN wc.total_price ELSE 0 END ) as converted_amount,
                SUM( wc.total_price ) as total_amount,
                SUM( CASE WHEN wo.website_cart_id IS NULL THEN 1 ELSE 0 END ) as abandoned_count,
                SUM( CASE WHEN wo.website_cart_id IS NOT NULL THEN 1 ELSE 0 END ) as converted_count,
                COUNT(*) as total_count
            FROM website_carts wc
            LEFT JOIN website_orders wo ON ( wc.website_cart_id = wo.website_cart_id )
            INNER JOIN (SELECT wci.website_cart_id, COUNT(*) FROM website_cart_items wci GROUP BY wci.website_cart_id) wct ON wc.website_cart_id = wct.website_cart_id
            WHERE wc.website_id = :website_id AND wc.email IS NOT NULL AND wc.timestamp >= :since"
            , 'i', [':website_id' => $website_id, ':since' => $since->format('Y-m-d')]
        )->get_row( PDO::FETCH_ASSOC );
    }

}
