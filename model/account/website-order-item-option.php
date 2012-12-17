<?php
class WebsiteOrderItemOption extends ActiveRecordBase {
    // The columns we will have access to
    public $website_order_item_id, $product_option_id, $product_option_list_item_id, $price, $option_type
        , $option_name, $list_item_value;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_order_item_options' );
    }

    /**
     * Get by order ID
     *
     * @param int $website_order_id
     * @return WebsiteOrderItemOption[]
     */
    public function get_by_order( $website_order_id ) {
        return $this->prepare(
            'SELECT woio.`website_order_item_id`, woio.`product_option_id`, woio.`product_option_list_item_id`, woio.`price`, woio.`option_type`, woio.`option_name`, woio.`list_item_value` FROM `website_order_item_options` AS woio LEFT JOIN `website_order_items` AS woi ON ( woi.`website_order_item_id` = woio.`website_order_item_id` ) WHERE woi.`website_order_id` = :website_order_id ORDER BY woio.`option_type` DESC'
            , 'i'
            , array( ':website_order_id' => $website_order_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteOrderItemOption' );
    }
}
