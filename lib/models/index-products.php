<?php

class IndexProducts extends ActiveRecordBase {

    public function __construct() {
        parent::__construct( 'index_products' );
    }

    public function index_website( $website_id ) {
        do {
            $deleted = $this->prepare("DELETE FROM index_products WHERE website_id = {$website_id} LIMIT 10000", '', array())->query()->get_row_count();
        } while ( $deleted > 0 );

        $this->prepare("SET autocommit=0;", '', array())->query();
        $this->prepare("SET unique_checks=0;", '', array())->query();
        $this->prepare("SET foreign_key_checks=0;", '', array())->query();

        $sql = $this->get_index_query( " AND w.website_id = {$website_id} ");
        $this->prepare( $sql, '', array() )->query();

        $this->prepare("COMMIT;", '', array())->query();
        $this->prepare("SET foreign_key_checks=1;", '', array())->query();
        $this->prepare("SET unique_checks=1;", '', array())->query();

    }

    public function index_product_all_websites($product_id) {
        do {
            $deleted = $this->prepare("DELETE FROM index_products WHERE product_id = {$product_id}", '', array())->query()->get_row_count();
        } while ( $deleted > 0 );

        $this->prepare("DELETE FROM index_products WHERE product_id = {$product_id}", '', array())->query();
        $sql = $this->get_index_query( " AND p.product_id = {$product_id} ", "w.website_id");
        $this->prepare( $sql, '', array() )->query();
    }

    public function index_product($product_id, $website_id) {
        $this->prepare("DELETE FROM index_products WHERE product_id = :product_id AND website_id = :website_id", 'ii', array(':product_id' => $product_id, ':website_id' => $website_id))->query();
        $sql = $this->get_index_query( " AND p.product_id = {$product_id} AND w.website_id = {$website_id} " );
        $this->prepare( $sql, '', array() )->query();
    }

    public function index_product_bulk($product_ids, $website_id) {
        if ( empty($product_ids) )
            return;

        $product_ids_in = implode( ',', $product_ids );

        $this->prepare("DELETE FROM index_products WHERE product_id IN ({$product_ids_in}) AND website_id = :website_id", 'i', array( ':website_id' => $website_id))->query();

        $sql = $this->get_index_query( " AND p.product_id IN ({$product_ids_in}) AND w.website_id = {$website_id} " );
        $this->prepare( $sql, '', array() )->query();
    }

    public function index_product_by_sku($skus, $website_id) {
        if ( empty($skus) )
            return;

        $skus_in = "'" . implode( "','", $skus ) . "'" ;

        $this->prepare("DELETE FROM index_products WHERE sku IN ($skus_in) AND website_id = :website_id", 'i', array( ':website_id' => $website_id))->query();

        $sql = $this->get_index_query( " AND p.sku IN ({$skus_in}) AND w.website_id = {$website_id} " );
        $this->prepare( $sql, '', array() )->query();
    }

    public function index_all() {
        $this->prepare("TRUNCATE TABLE index_products ", '', array())->query();
        $sql = $this->get_index_query( "", "w.website_id" );
        $this->prepare( $sql, '', array() )->query();
    }

    private function get_index_query($where = null, $group_by = null) {
        if ( $group_by )
            $group_by = ', ' . $group_by;

        return "INSERT INTO index_products(
                product_id, category_id, brand_id, industry_id, website_id, name, slug, description,
                sku, price, price_min, weight, volume, product_specifications, status, alternate_price,
                sale_price, wholesale_price, inventory, additional_shipping_amount, protection_amount, additional_shipping_type,
                alternate_price_name, meta_title, meta_description, meta_keywords, protection_type, price_note,
                product_note, ships_in, store_sku, warranty_length, alternate_price_strikethrough, display_inventory, on_sale,
                sequence, manual_price, setup_fee, date_updated, brand, category, industry,
                tags, is_ashley_express, image, attribute_items)
            SELECT
                p.product_id, p.category_id, p.brand_id, p.industry_id, wp.website_id, p.name, p.slug, p.description, p.sku,
                wp.price, p.price_min, wp.weight, p.volume, p.product_specifications, p.status, wp.alternate_price,
                wp.sale_price, wp.wholesale_price, wp.inventory, wp.additional_shipping_amount, wp.protection_amount, wp.additional_shipping_type,
                wp.alternate_price_name, wp.meta_title, wp.meta_description, wp.meta_keywords, wp.protection_type, wp.price_note,
                wp.product_note, wp.ships_in, wp.store_sku, wp.warranty_length, wp.alternate_price_strikethrough, wp.display_inventory, wp.on_sale,
                wp.sequence, wp.manual_price, wp.setup_fee, now(), b.name, c.name, i.name,
                GROUP_CONCAT(DISTINCT t.value),
                CASE WHEN wpae.product_id IS NULL THEN 1 ELSE 0 END,
                pi.image,
                CONCAT( '|', GROUP_CONCAT( DISTINCT air.attribute_item_id SEPARATOR '|' ), '|' )
            FROM products p
            INNER JOIN website_products wp ON wp.product_id = p.product_id
            INNER JOIN categories c ON p.category_id = c.category_id
            INNER JOIN brands b ON b.brand_id = p.brand_id
            INNER JOIN industries i ON i.industry_id = p.industry_id
            INNER JOIN product_images pi ON pi.product_id = p.product_id
            INNER JOIN websites w ON w.website_id = wp.website_id
            LEFT JOIN website_blocked_category wbc ON wbc.category_id = p.category_id AND wbc.website_id = wp.website_id
            LEFT JOIN website_product_ashley_express wpae ON wpae.product_id = wp.product_id AND wpae.website_id = wp.website_id
            LEFT JOIN tags t ON t.type = 'product' AND t.object_id = p.product_id
            LEFT JOIN attribute_item_relations air ON air.product_id = p.product_id
            WHERE p.publish_visibility = 'public'
                AND pi.sequence = 0
                AND wp.blocked = 0
                AND wp.active = 1
                AND w.status = 1
                AND w.live = 1
                AND wbc.category_id IS NULL
                $where
            GROUP BY p.product_id $group_by";
    }

}