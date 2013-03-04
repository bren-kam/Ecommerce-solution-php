<?php
class WebsiteOrderItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_order_item_id, $website_order_id, $product_id, $name, $sku, $quantity, $price
        , $additional_shipping_price, $protection_price, $extra, $price_note, $product_note, $ships_in
        , $store_sku, $warranty_length, $status;

    // Belongs to other tables
    public $industry, $image;

    /**
     * @var WebsiteOrderItemOption[]
     */
    public $product_options;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_order_items' );

        // We want to make sure they match
        if ( isset( $this->website_order_item_id ) )
            $this->id = $this->website_order_item_id;
    }

    /**
     * Get all
     *
     * @param int $website_order_id
     * @return WebsiteOrderItem[]
     */
    public function get_all( $website_order_id ) {
        // Get the main order
        $items_array = $this->get_by_order( $website_order_id );
        $items = array();

        // Organize options
        $website_order_item_option = new WebsiteOrderItemOption();
        $item_options_array = $website_order_item_option->get_by_order( $website_order_id );
        $item_options = array();

        /**
         * @var WebsiteOrderItemOption $option
         */
        foreach ( $item_options_array as $option ) {
            $item_options[$option->website_order_item_id][] = $option;
        }

        /**
         * @var WebsiteOrderItem $item
         */
        if ( is_array( $items_array ) && !empty( $items_array ) )
        foreach ( $items_array as $item ) { // Populate local variables
            $image_link = 'http://' . $item->industry . '.retailcatalog.us/products/' . $item->product_id . '/' . $item->image;

            $items[$item->id] = $item;
            $items[$item->id]->image = $image_link;
            $items[$item->id]->product_options = ( isset( $item_options[$item->id] ) ) ? $item_options[$item->id] : array();
            $items[$item->id]->extra = unserialize( $item->extra );
        }

        return $items;
    }

    /**
     * Get by order ID
     *
     * @param int $website_order_id
     * @return array
     */
    public function get_by_order( $website_order_id ) {
        return $this->prepare(
            'SELECT woi.*, i.`name` AS industry, pi.`image` FROM `website_order_items` AS woi INNER JOIN `products` AS p ON ( p.`product_id` = woi.`product_id` ) INNER JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) WHERE woi.`website_order_id` = :website_order_id AND ( pi.`sequence` = 0 OR pi.`sequence` IS NULL )'
            , 'i'
            , array( ':website_order_id' => $website_order_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteOrderItem' );
    }
}
