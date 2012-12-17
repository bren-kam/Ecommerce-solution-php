<?php
class WebsiteOrder extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_order_id, $website_id, $website_user_id, $website_cart_id, $website_shipping_method_id
        , $website_coupon_id, $shipping_price, $tax_price, $coupon_discount, $total_cost, $email, $phone
        , $billing_first_name, $billing_last_name, $billing_address1, $billing_address2, $billing_city
        , $billing_state, $billing_zip, $billing_phone, $billing_alt_phone, $shipping_first_name
        , $shipping_last_name, $shipping_address1, $shipping_address2, $shipping_city, $shipping_state
        , $shipping_zip, $status, $date_created;

    // Belonging to another table
    public $shipping_method;

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
     *
     * @param int $website_order_id
     * @param int $account_id
     */
    public function get_complete( $website_order_id, $account_id ) {
        // Get the main order
        $this->get( $website_order_id, $account_id );

        // Get items
        $website_order_item = new WebsiteOrderItem();
        $this->items = $website_order_item->get_all( $this->id );
    }

    /**
     * Get
     * @param int $website_order_id
     * @param int $account_id
     */
    public function get( $website_order_id, $account_id ) {
        $this->prepare(
            'SELECT wo.*, wsm.`name` AS shipping_method FROM `website_orders` AS wo LEFT JOIN `website_shipping_methods` AS wsm ON ( wsm.`website_shipping_method_id` = wo.`website_shipping_method_id` ) WHERE wo.`website_order_id` = :website_order_id AND wo.`website_id` = :account_id'
            , 'ii'
            , array( ':website_order_id' => $website_order_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_order_id;
    }

    /**
     * Save
     */
    public function save() {
        parent::update( array(
                'status' => $this->status
            ), array(
                'website_order_id' => $this->id
            ), 'i', 'i'
        );
    }

    /**
	 * List Users
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `website_order_id`, `total_cost`, `status`, `date_created` FROM `website_orders` WHERE 1 $where $order_by LIMIT $limit"
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
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'website_user_id' => $this->id
        ), 'i' );
    }
}
