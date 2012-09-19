<?php
class AshleyReport extends CustomReport {
    /**
     * Setup
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the Ashley Custom Report
     *
     * @return array
     */
    public function report() {
        $products = $this->get_results( "SELECT a.`sku`, CONCAT( 'http://admin." . DOMAIN . "/products/add-edit/?pid=', a.`product_id` ), IF ( 'private' = a.`publish_visibility`, 'Yes', 'No' ), IF ( b.`category_id` IS NOT NULL, 'Yes', 'No' ),  IF ( c.`attribute_item_id` IS NOT NULL, 'Yes', 'No' ), IF ( d.`product_image_id` IS NOT NULL, 'Yes', 'No' ) FROM `products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `attribute_item_relations` AS c ON ( a.`product_id` = c.`product_id` ) LEFT JOIN `product_images` AS d ON ( a.`product_id` = d.`product_id` AND d.`sequence` = 0 ) WHERE a.`user_id_created` = 353 AND a.`publish_visibility` <> 'deleted' AND ( a.`publish_visibility` = 'private' OR b.`category_id` IS NULL OR c.`attribute_item_id` IS NULL OR d.`product_image_id` IS NULL )", PDO::FETCH_ASSOC );

        array_unshift( $products, array( 'SKU', 'Link', 'Private', 'Categories', 'Attributes', 'Product Images' ) );

        return $products;
    }
}
