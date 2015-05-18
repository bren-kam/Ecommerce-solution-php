<?php
class WebsiteCartItemOption extends ActiveRecordBase {
    // The columns we will have access to
    public $website_cart_item_id, $product_option_id, $product_option_list_item_id, $price, $option_type
        , $option_name, $list_item_value;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_cart_item_options' );
    }

    /**
     * Get by cart ID
     *
     * @param int $website_cart_id
     * @return WebsiteCartItemOption[]
     */
    public function get_by_cart( $website_cart_id ) {
        return $this->prepare(
            'SELECT wcio.`website_cart_item_id`, wcio.`product_option_id`, wcio.`product_option_list_item_id`, po.`option_type`, po.`option_name`, poli.`value`
              FROM `website_cart_item_options` AS wcio
              LEFT JOIN `website_cart_items` AS wci ON ( wci.`website_cart_item_id` = wcio.`website_cart_item_id` )
              LEFT JOIN `product_options` po ON ( po.product_option_id = wcio.product_option_id )
              LEFT JOIN `product_option_list_items` poli ON ( poli.product_option_list_item_id = wcio.product_option_list_item_id )
              WHERE wci.`website_cart_id` = :website_cart_id
              ORDER BY po.`option_type` DESC'
            , 'i'
            , array( ':website_cart_id' => $website_cart_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteCartItemOption' );
    }
}
