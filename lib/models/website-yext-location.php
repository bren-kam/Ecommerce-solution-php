<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextLocation extends ActiveRecordBase {

    public $yext_id, $website_id, $synchronize_products;

    public function __construct() {
        parent::__construct( 'website_yext_location' );
    }

    /**
     * Get
     * @param $yext_id
     * @param $website_id
     */
    public function get( $yext_id, $website_id ) {
        $this->prepare(
            "SELECT * FROM website_yext_location WHERE yext_id = :yext_id AND website_id = :websiteid"
            , 'i'
            , [  ':yext_id' => $yext_id, ':website_id' => $website_id ]
        )->get_row( PDO::FETCH_INTO, $this );
    }


    /**
     * Get All
     * @param $website_id
     * @return WebsiteYextLocation[]
     */
    public function get_all( $website_id ) {
        return $this->prepare(
            "SELECT * FROM website_yext_location WHERE website_id = :websiteid"
            , 'i'
            , [ ':website_id' => $website_id ]
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextLocation' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert(
            [
                'yext_id' => $this->yext_id
                , 'website_id' => $this->website_id
                , 'synchronize_products' => $this->synchronize_products
            ]
            , 'iii'
        );
    }

    /**
     * Save
     */
    public function save() {
        $this->update(
            [
                'synchronize_products' => $this->synchronize_products
            ]
            , [  ':yext_id' => $this->yext_id, ':website_id' => $this->website_id ]
            , 'i'
            , 'ii'
        );
    }

    /**
     * Delete
     */
    public function delete() {
        parent::delete(
            [  ':yext_id' => $this->yext_id, ':website_id' => $this->website_id ]
            , 'ii'
        );
    }

    /**
     * Synchronize Products All
     * Calls synchronize_products() on all WebsiteYextLocation
     */
    public function synchronize_products_all() {

        $locations = $this->get_results(
            "SELECT * FROM website_yext_location WHERE synchronize_products = 1"
            , PDO::FETCH_CLASS
            , 'WebsiteYextLocation'
        );

        foreach ( $locations as $location ) {
            $this->synchronize_products( $location );
        }

    }

    /**
     * Synchronize Products
     * Send top 100 products to YEXT for a specific WebsiteYextLocation
     *
     * @param WebsiteYextLocation $location
     */
    public function synchronize_products( $location ) {

        // Get Website Top Products
        $product_ids = $this->get_results(
            "SELECT product_id FROM website_product_view WHERE website_id = {$location->website_id} GROUP BY product_id ORDER BY COUNT(*) DESC LIMIT 100"
            , PDO::FETCH_COLUMN
        );

        if ( empty( $product_ids ) )
            return;

        $category = new Category();
        $category->get_all();

        $product_ids_sql = implode( ',', $product_ids );
        $products = $this->get_results(
            "SELECT p.product_id, p.sku, p.name, p.description, COALESCE( wp.sale_price, wp.price, p.price ) AS price, pi.image, p.category_id, p.industry_id
             FROM products p
             INNER JOIN website_products wp ON p.product_id = wp.product_id
             INNER JOIN products_images pi ON p.product_id = pi.product_id
             WHERE wp.website_id = {$location->website_id}
               AND wp.product_id IN ({$product_ids_sql})
               AND p.publish_visibility = 'public' AND wp.blocked = 0 AND wp.active = 1 AND pi.sequence = 0
             ", PDO::FETCH_ASSOC
        );

        $yext_lists_items = [];

        foreach ( $products as &$product ) {

            $parent_categories = $category->get_all_parents( $product['category_id'] );
            $parent_category = array_pop( $parent_categories );

            $yext_lists_items[ $parent_category->id ] = [
                'id' => $product['product_id']
                , 'name' => $product['name']
                , 'description' => $product['description']
                , 'idCode' => $product['sku']
                , 'cost' => [
                    'type' => 'PRICE'
                    , 'price' => $product['price']
                    , 'unit' => 'Each'
                ]
                , 'photo' => [
                    'url' => $product['image']
                    , 'width' => 200
                    , 'height' => 200
                ]
            ];

        }

        foreach ( $yext_lists_items as $parent_category_id => &$yext_items ) {
            $parent_category = Category::$categories[$parent_category_id];
            $yext_list = [
                'id' => "{$location->website_id}-list-products-{$parent_category_id}"
                , 'name' => 'Products'
                , 'title' => $parent_category->name
                , 'type' => 'PRODUCTS'
                , 'publish' => true
                , 'sections' => [
                    'id' => "{$location->website_id}-list-products-{$parent_category_id}-section"
                    , 'name' => $parent_category->name
                    , 'items' => $yext_items
                ]
            ];

            // TODO: send to YEXT
        }

        // Cleanup
        unset( $yext_lists_items );
        unset( $products );

    }

}
