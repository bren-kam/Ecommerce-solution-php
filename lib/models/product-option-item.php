<?php

class ProductOptionItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $product_option_id, $name;

    // Artificial fields
    public $product_option, $product_name, $price_wholesale, $price_map, $price_sale, $price_msrp;

    // Fields from other tables
    public $product_id, $parent_product_id, $price;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_option_item' );
    }

    /**
     * Creates a Produt Option Item
     */
    public function create() {
        $this->id = $this->insert([
            'product_option_id' => $this->product_option_id
            , 'name' => $this->name
        ], 'is' );
    }

    /**
     * Saves a Product Option Item
     */
    public function save() {
        $this->update([
            'name' => $this->name
        ], [
            'id' => $this->id
        ], 's', 'i' );
    }

    /**
     * Get by id
     *
     * @param int $product_option_item_id
     * @param int $product_option_id
     */
    public function get( $product_option_item_id, $product_option_id ) {
        $this->prepare(
            'SELECT * FROM `product_option_item` WHERE `id` = :product_option_item_id AND `product_option_id` = :product_option_id'
            , 'ii'
            , array( ':product_option_item_id' => $product_option_item_id, ':product_option_id' => $product_option_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get by product option
     *
     * @param int $product_option_id
     * @return ProductOptionItem[]
     */
    public function get_by_product_option( $product_option_id ) {
        return $this->prepare(
            'SELECT * FROM `product_option_item` WHERE `product_option_id` = :product_option_id'
            , 'i'
            , array( ':product_option_id' => $product_option_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOptionItem' );
    }

    /**
     * Export data
     *
     * @param int $website_id
     * @return ProductOptionItem[]
     */
    public function export( $website_id ) {
        return $this->prepare(
            "SELECT GROUP_CONCAT(CONCAT(po.`name`,'=',poi.`name`)) as product_option, poip.`product_id`, p.`parent_product_id`, p.`sku`, p.`name` AS product_name, p.`description`,  p.`price` AS price_wholesale, p.`price_min` as price_map, wp.`price`, wp.`sale_price` AS price_sale, wp.`alternate_price` AS price_msrp, COALESCE( CONCAT('http://', i.`name`, '.retailcatalog.us/products/', p.`product_id`, '/large/', pi.`image`), '') AS image FROM `product_option_item` as poi LEFT JOIN `product_option` AS po ON ( po.`id` = poi.`product_option_id` ) LEFT JOIN `product_option_item_product` AS poip ON ( poip.`product_option_item_id` = poi.`id` ) LEFT JOIN `website_products` AS wp ON ( wp.`website_id` = po.`website_id` AND wp.`product_id` = poip.`product_id` ) LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` AND pi.`sequence` = 0 ) WHERE po.`website_id` = :website_id AND wp.`active` = 1 AND p.`publish_visibility` = :publish_visibility GROUP BY poip.`product_id`"
            , 'is'
            , array( ':website_id' => $website_id, ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC )
        )->get_results( PDO::FETCH_CLASS, 'ProductOptionItem' );
    }

    /**
     * Add Relations
     *
     * @param array $product_ids
     */
    public function add_relations( array $product_ids ) {
        if ( !$product_ids )
            return;

        $product_ids_count = count( $product_ids );
        $values = substr( str_repeat( ',(' . (int) $this->id . ', ? )', $product_ids_count ), 1 );

        $this->prepare(
            'INSERT INTO `product_option_item_product` (`product_option_item_id`, `product_id`) VALUES ' . $values ' ON DUPLICATE KEY UPDATE `product_id` = VALUES(`product_id`)'
            , str_repeat( 's', count( $product_ids ) )
            , $product_ids
        )->query();
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete_associated_products();
        $this->remove_self();
    }

    /**
     * Remove self
     */
    protected function remove_self() {
        $this->delete([
            'id' => $this->id
        ], 'i' );
    }

    /**
     * Delete Associated Products
     */
    protected function delete_associated_products() {
        $this->prepare(
            'DELETE p.* FROM `products` p LEFT JOIN `product_option_item_product` poip ON ( poip.`product_id` = p.`product_id` ) WHERE poip.`product_option_item_id` = :product_option_item_id'
            , 'i'
            , [':product_option_item_id' => $this->id]
        )->query();
    }
}