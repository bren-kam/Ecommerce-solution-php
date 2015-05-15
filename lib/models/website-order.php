<?php
class WebsiteOrder extends ActiveRecordBase {
    const STATUS_DECLINED = -1;
    const STATUS_PURCHASED = 0;
    const STATUS_PENDING = 1;
    const STATUS_DELIVERED = 2;

    const STATUS_RECEIVED = 3;  // Ashley Express - Order Received by External Service
    const STATUS_SHIPPED = 4;   // Ashley Express - Order Shipped

    // The columns we will have access to
    public $id, $website_order_id, $website_id, $website_user_id, $website_cart_id, $website_shipping_method_id, $website_ashley_express_shipping_method_id
        , $website_coupon_id, $shipping_price, $tax_price, $coupon_discount, $total_cost, $email, $phone
        , $billing_first_name, $billing_last_name, $billing_address1, $billing_address2, $billing_city
        , $billing_state, $billing_zip, $billing_phone, $billing_alt_phone, $shipping_name, $shipping_first_name
        , $shipping_last_name, $shipping_address1, $shipping_address2, $shipping_city, $shipping_state
        , $shipping_zip, $status, $date_created, $shipping_track_number, $authorize_only;

    // Artificial field
    public $name;

    // Belonging to another table
    public $shipping_method, $ashley_express_shipping_method;

    /**
     * @var WebsiteOrderItem[]
     */
    public $items;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_orders' );

        // We want to make sure they match
        if ( isset( $this->website_order_id ) )
            $this->id = $this->website_order_id;
    }

    /**
     * Get
     * @param int $website_order_id
     * @param int $account_id
     */
    public function get( $website_order_id, $account_id ) {
        $this->prepare(
            "SELECT wo.*, IF( '' = wo.`shipping_name`, CONCAT( wo.`shipping_first_name`, ' ', wo.`shipping_last_name` ), wo.`shipping_name` ) AS shipping_name, wsm.`name` AS shipping_method, wsm_ashley_express.`name` AS ashley_express_shipping_method FROM `website_orders` AS wo LEFT JOIN `website_shipping_methods` AS wsm ON ( wsm.`website_shipping_method_id` = wo.`website_shipping_method_id` ) LEFT JOIN `website_shipping_methods` AS wsm_ashley_express ON ( wsm_ashley_express.`website_shipping_method_id` = wo.`website_ashley_express_shipping_method_id` ) WHERE wo.`website_order_id` = :website_order_id AND wo.`website_id` = :account_id"
            , 'ii'
            , array( ':website_order_id' => $website_order_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_order_id;
    }

    /**
     * Get
     *
     * @param int $website_order_id
     * @param int $account_id
     * @param WebsiteOrderItem $website_order_item [optional for testing]
     */
    public function get_complete( $website_order_id, $account_id, WebsiteOrderItem $website_order_item = null ) {
        // Get the main order
        $this->get( $website_order_id, $account_id );

        // Get items
        if ( is_null( $website_order_item ) )
            $website_order_item = new WebsiteOrderItem();

        $this->items = $website_order_item->get_all( $this->id );
    }

    /**
     * Save
     */
    public function save() {
        parent::update( array(
            'status' => $this->status
            , 'shipping_track_number' => $this->shipping_track_number
        ), array(
            'website_order_id' => $this->id
        ), 'i', 'i' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'website_order_id' => $this->id
        ), 'i' );
    }

    /**
	 * List Website Orders
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return WebsiteOrder[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `website_order_id`, website_id, IF ( '' = `billing_first_name`, `shipping_name`, CONCAT( `billing_first_name`, ' ', `billing_last_name` ) ) AS name, `total_cost`, `status`, `date_created`, `website_shipping_method_id`, `website_ashley_express_shipping_method_id` FROM `website_orders` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteOrder' );
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
        return $this->prepare( "SELECT COUNT( `website_order_id` )  FROM `website_orders` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
     * Is Ashley Express
     * @return bool
     */
    public function is_ashley_express() {
        if ( $this->website_ashley_express_shipping_method_id > 0 )
            return true;

        $sm = new WebsiteShippingMethod();
        $sm->get( $this->website_shipping_method_id, $this->website_id );
        if ( $sm->type == 'ashley-express-ups' || $sm->type == 'ashley-express-fedex' )
            return true;

        return (bool) $this->get_var("SELECT COUNT(DISTINCT ae.product_id)
            FROM website_orders wo
            INNER JOIN website_order_items woi ON wo.website_order_id = woi.website_order_id
            INNER JOIN website_product_ashley_express ae ON woi.product_id = ae.product_id AND wo.website_id = ae.website_id
            WHERE wo.website_order_id = '{$this->website_order_id}'");
    }

    /**
     * Get By Account
     * @param $account_id
     * @return WebsiteOrder[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            "SELECT wo.*, IF( '' = wo.`shipping_name`, CONCAT( wo.`shipping_first_name`, ' ', wo.`shipping_last_name` ), wo.`shipping_name` ) AS shipping_name, wsm.`name` AS shipping_method, wsm_ashley_express.`name` AS ashley_express_shipping_method FROM `website_orders` AS wo LEFT JOIN `website_shipping_methods` AS wsm ON ( wsm.`website_shipping_method_id` = wo.`website_shipping_method_id` ) LEFT JOIN `website_shipping_methods` AS wsm_ashley_express ON ( wsm_ashley_express.`website_shipping_method_id` = wo.`website_ashley_express_shipping_method_id` ) WHERE wo.`website_id` = :account_id"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteOrder');
    }
}
