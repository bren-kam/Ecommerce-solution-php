<?php
class WebsiteCartItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_cart_item_id, $website_cart_id, $product_id, $name, $sku, $quantity, $price
        , $additional_shipping_price, $extra, $price_note, $product_note, $ships_in
        , $store_sku, $warranty_length, $status;

    // Belongs to other tables
    public $industry, $image, $product_sku;

    /**
     * @var WebsiteCartItemOption[]
     */
    public $product_options;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_cart_items' );

        // We want to make sure they match
        if ( isset( $this->website_cart_item_id ) )
            $this->id = $this->website_cart_item_id;
    }

    /**
     * Get by Cart ID
     *
     * @param int $website_cart_id
     * @return WebsiteCartItem[]
     */
    public function get_by_cart( $website_cart_id ) {
        return $this->prepare(
            'SELECT wci.*, i.`name` AS industry, pi.`image`, p.`sku` AS product_sku, (CASE WHEN wp.sale_price > 0 THEN wp.sale_price ELSE wp.price END) as price
              FROM `website_cart_items` AS wci
              INNER JOIN `products` AS p ON ( p.`product_id` = wci.`product_id` )
              INNER JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` )
              LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` )
              INNER JOIN `website_carts` wc ON ( wc.`website_cart_id` = wci.`website_cart_id` )
              INNER JOIN `website_products` wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = wc.`website_id` )
              WHERE wci.`website_cart_id` = :website_cart_id AND ( pi.`sequence` = 0 OR pi.`sequence` IS NULL )'
            , 'i'
            , array( ':website_cart_id' => $website_cart_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteCartItem' );
    }

    /**
     * Get all
     *
     * @param int $website_cart_id
     * @param WebsiteCartItemOption $website_cart_item_option [optional for testing]
     * @return WebsiteCartItem[]
     */
    public function get_all( $website_cart_id, WebsiteCartItemOption $website_cart_item_option = null ) {
        $product = new AccountProduct();

        // Get the main cart
        $items_array = $this->get_by_cart( $website_cart_id );
        $items = array();

        // Organize options
        if ( is_null( $website_cart_item_option ) )
            $website_cart_item_option = new WebsiteCartItemOption();

        $item_options_array = $website_cart_item_option->get_by_cart( $website_cart_id );
        $item_options = array();

        /**
         * @var WebsiteCartItemOption $option
         */
        foreach ( $item_options_array as $option ) {
            $item_options[$option->website_cart_item_id][] = $option;
        }

        /**
         * @var WebsiteCartItem $item
         */
        if ( is_array( $items_array ) && !empty( $items_array ) )
        foreach ( $items_array as $item ) { // Populate local variables
            $image_link = $product->get_image_url( $item->image, '', $item->industry, $item->product_id );

            $items[$item->id] = $item;
            $items[$item->id]->image = $image_link;
            $items[$item->id]->product_options = ( isset( $item_options[$item->id] ) ) ? $item_options[$item->id] : array();
            $items[$item->id]->extra = unserialize( $item->extra );
        }

        return $items;
    }
}
