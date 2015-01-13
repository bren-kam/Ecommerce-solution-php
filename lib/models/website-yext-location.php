<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextLocation extends ActiveRecordBase {

    public $id, $website_yext_location_id, $website_id, $synchronize_products, $name, $address, $last_update, $status, $customm_photos;

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
            "SELECT * FROM website_yext_location WHERE website_yext_location_id = :yext_id AND website_id = :website_id"
            , 'i'
            , [  ':yext_id' => $yext_id, ':website_id' => $website_id ]
        )->get_row( PDO::FETCH_INTO, $this );
        $this->id = $this->website_yext_location_id;
    }


    /**
     * Get All
     * @param $website_id
     * @return WebsiteYextLocation[]
     */
    public function get_all( $website_id ) {
        $all = $this->prepare(
            "SELECT * FROM website_yext_location WHERE website_id = :website_id"
            , 'i'
            , [ ':website_id' => $website_id ]
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextLocation' );

        foreach ( $all as $l ) {
            $l->id = $l->website_yext_location_id;
        }
        return $all;
    }

    /**
     * List All
     * @param $variables
     * @return WebsiteYextLocation[]
     */
    public function list_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        $all = $this->prepare(
            "SELECT * FROM website_yext_location WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextLocation' );

        foreach ( $all as $l ) {
            $l->id = $l->website_yext_location_id;
        }
        return $all;
    }

    /**
     * Count All
     * @param $variables
     * @return int
     */
    public function count_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT COUNT(*) FROM website_yext_location WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /**
     * Create
     */
    public function create() {
        $this->id = $this->website_yext_location_id = $this->insert(
            [
                'website_id' => $this->website_id
                , 'name' => $this->name
                , 'address' => $this->address
                , 'synchronize_products' => $this->synchronize_products
                , 'status' => $this->status
            ]
            , 'issis'
        );
    }

    /**
     * Save
     */
    public function save() {
        $this->update(
            [
                'synchronize_products' => $this->synchronize_products
                , 'name' => $this->name
                , 'address' => $this->address
                , 'status' => $this->status
            ]
            , [  'website_yext_location_id' => $this->id, 'website_id' => $this->website_id ]
            , 'isss'
            , 'ii'
        );
    }

    /**
     * remove
     */
    public function remove() {
        parent::delete(
            [  'website_yext_location_id' => $this->id, 'website_id' => $this->website_id ]
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
    public function do_synchronize_products( $location ) {

        // Get Website Top Products
        $product_ids = $this->get_results(
            "SELECT product_id FROM website_product_view WHERE website_id = {$location->website_id} AND product_id IS NOT NULL GROUP BY product_id ORDER BY COUNT(*) DESC LIMIT 100"
            , PDO::FETCH_COLUMN
        );

        if ( empty( $product_ids ) )
            return;

        $account = new Account();
        $account->get( $location->website_id );

        $product_obj = new Product();

        $category = new Category();
        $category->get_all();

        $product_ids_sql = implode( ',', $product_ids );
        $products = $this->get_results(
            "SELECT p.product_id, p.sku, p.slug, p.name, p.description, COALESCE( wp.sale_price, wp.price, p.price ) AS price, pi.image, p.category_id, p.industry_id, i.name as industry
             FROM products p
             INNER JOIN website_products wp ON p.product_id = wp.product_id
             INNER JOIN product_images pi ON p.product_id = pi.product_id
             INNER JOIN industries i ON p.industry_id = i.industry_id
             WHERE wp.website_id = {$location->website_id}
               AND wp.product_id IN ({$product_ids_sql})
               AND p.publish_visibility = 'public' AND wp.blocked = 0 AND wp.active = 1 AND pi.sequence = 0
             LIMIT 100"
            , PDO::FETCH_ASSOC
        );

        $yext_lists_items = [];

        foreach ( $products as &$product ) {

            $parent_categories = $category->get_all_parents( $product['category_id'] );
            $parent_category = array_pop( $parent_categories );

            if ($account->is_new_template() ) {
                $product['link'] = 'http://' . $account->domain . '/product' . ( ( 0 == $product['category_id'] ) ? '/' . $product['slug'] : $category->get_url( $product['category_id'] ) . $product['slug'] . '/' );
            } else {
                $product['link'] = 'http://' . $account->domain . ( ( 0 == $product['category_id'] ) ? '/' . $product['slug'] : $category->get_url( $product['category_id'] ) . $product['slug'] . '/' );
            }

            $product['image'] = $product_obj->get_image_url( $product['image'], 'small', $product['industry'], $account->id );

            $yext_lists_items[ $parent_category->id ][] = [
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
                , 'url' => $product['link']
            ];

        }

        $yext_list_id = "{$location->website_id}-{$location->website_yext_location_id}-products";
        $yext_list = [
            'id' => $yext_list_id
            , 'name' => "Products for {$account->title}. {$yext_list_id}    "
            , 'title' => "Products for {$account->title}"
            , 'type' => 'PRODUCTS'
            , 'publish' => true
            , 'sections' => []
        ];
        foreach ( $yext_lists_items as $parent_category_id => &$yext_items ) {
            $parent_category = Category::$categories[$parent_category_id];
            $yext_list['sections'][] = [
                'id' => "{$yext_list_id}-{$parent_category_id}-section"
                , 'name' => $parent_category->name
                , 'items' => $yext_items
            ];
        }

        library('yext');
        $yext = new YEXT( $account );
        $yext_list_exists = $yext->get( "lists/{$yext_list_id}" );
        if ( isset( $yext_list_exists->id ) ) {
            $response = $yext->put( "lists/{$yext_list_id}", $yext_list );
        } else {
            $response = $yext->post( "lists", $yext_list );
        }

        $yext_location = (array) $yext->get("locations/{$location->id}");
        if ( empty($yext_location['lists']) ) {
            $yext_location['lists'] = [[
                'id' => $yext_list['id']
                , 'name' => $yext_list['name']
                , 'type' => 'PRODUCTS'
            ]];
        } else {
            $found = false;
            foreach( $yext_location['lists'] as $list ) {
                if ( $list->name == $yext_list['name'] ) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $yext_location['lists'][] = [
                    'id' => $yext_list['id']
                    , 'name' => $yext_list['name']
                    , 'type' => 'PRODUCTS'
                ];
            }
        }
        $yext->put("locations/{$location->id}", $yext_location);

        // Cleanup
        unset( $yext_lists_items );
        unset( $products );

    }

    /**
     * Do Upload Photos
     * @param WebsiteYextLocation $location
     */
    public function do_upload_photos( $location ) {
        $account = new Account();
        $account->get( $location->website_id );

        library( 'yext' );
        $yext = new Yext( $account );
        $yext_location  = $yext->get( "locations/{$location->id}" );

        if ( isset( $yext_location->errors) )
            return;

        $attachment = new AccountPageAttachment();
        $page = new AccountPage();

        $page->get_by_slug( $account->id, 'home' );
        $banner_page_id = $page->id;
        $page->get_by_slug( $this->id, 'sidebar' );
        $sidebar_page_id = $page->id;

        $attachments = $attachment->get_by_account_page_ids( [ $banner_page_id, $sidebar_page_id ] );
        $images = [];
        foreach ( $attachments as $attachment ) {
            if ($attachment->status == 0) {
                continue;
            }

            $extra = json_decode($attachment->extra, true);
            // is it json?
            if ($extra) {
                // Verify Date Range
                if (isset($extra['date-range']) && $extra['date-range']) {
                    // date range
                    $now = date('Y-m-d');

                    // out of range?
                    if ($extra['date-start'] > $now || $extra['date-end'] < $now) {
                        continue;
                    }
                }
            }

            if ($attachment->key == 'banner' || $attachment->key == 'sidebar-image') {
                $images[] = [ 'url' => $attachment->value ];
            }
        }

        if ( empty( $images ) ) {
            return;
        }

        $images = array_reverse( $images );

        $current_image_list = isset( $yext_location->photos ) ? $yext_location->photos : [];
        $new_image_list = array_merge(
            $current_images,
            $images
        );
        $new_image_list = array_slice( $new_image_list, 0, 5 );

        $yext_location->photos = $new_image_list;
        $yext->put( "locations/{$location->id}", $yext_location );

    }

}
