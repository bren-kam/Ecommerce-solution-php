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
        $products = $this->get_results( "SELECT p.`sku`, CONCAT( 'http://admin." . DOMAIN . "/products/add-edit/?pid=', p.`product_id` ), IF ( 'private' = p.`publish_visibility`, 'Yes', 'No' ), IF ( p.`category_id` IS NOT NULL, 'Yes', 'No' ),  IF ( air.`attribute_item_id` IS NOT NULL, 'Yes', 'No' ), IF ( pi.`product_image_id` IS NOT NULL, 'Yes', 'No' ) FROM `products` AS p LEFT JOIN `attribute_item_relations` AS air ON ( air.`product_id` = p.`product_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` AND pi.`sequence` = 0 ) WHERE p.`user_id_created` = 353 AND p.`publish_visibility` <> 'deleted' AND ( p.`publish_visibility` = 'private' OR p.`category_id` = 0 OR air.`attribute_item_id` IS NULL OR pi.`product_image_id` IS NULL )", PDO::FETCH_ASSOC );

        array_unshift( $products, array( 'SKU', 'Link', 'Private', 'Categories', 'Attributes', 'Product Images' ) );

        return $products;
    }
}
