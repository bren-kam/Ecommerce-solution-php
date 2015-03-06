<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 9/18/14
 * Time: 2:00 PM
 */

class RMInnovationsFeedGateway extends ActiveRecordBase {

    const DB_URL = 'RMInnovations';  // This is a FreeTDS DSN Name
    const DB_USER = 'ImagineRetailer';
    const DB_PASS = '{)@Pfg54SV@e';
    const DB_NAME = 'ImagineRetailer';

    const BRAND_ID = 920;
    const USER_ID = 2894;

    private $category_map = array(
        'Rug' => 'Accessories > Rugs',
        'Pad' => 'Accessories > Pads',
        'Ottoman' => 'Living Room > Ottomans',
        'Pelt' => 'Accessories > Pelt',
        'Mat' => 'Accessories > Mats',
        'Pillow' => 'Accessories > Pillows',
        'Pouf' => 'Accessories > Poufs',
        'Chairpad' => 'Accessories > Chairpad',
        'Stair Tread' => 'Accessories > Stair Tread',
        'Textile' => 'Accessories > Textile',
        'Hardware' => 'Accessories > Hardware',
        'Basket' => 'Accessories > Baskets',
        'Care' => 'Accessories > Care',
        'Tile' => 'Accessories > Tile',
        'Accessory' => 'Accessories > Accessory',
        'Furniture' => 'Living Room > Furniture',
    );

    private $categories_by_name = array();

    private $current_product_list = array();

    private $brands = array();

    public function __construct() {
        // Load Categories
        $category = new Category();
        $categories = $category->get_all();
        $categories_by_name = array();
        foreach ( $categories as $category ) {
            if ( $category->has_children() )
                continue;

            $category_string = $category->name;
            $parents = $category->get_all_parents( $category->id );

            foreach ( $parents as $parent_category ) {
                $category_string = $parent_category->name . ' > ' . $category_string;
            }

            $categories_by_name[$category_string] = $category->id;
        }
        ksort( $categories_by_name );
        $this->categories_by_name = $categories_by_name;

        $this->current_product_list = $this->get_current_products();

        $brand = new Brand();
        $brand_list = $brand->get_all();
        foreach ( $brand_list as $b ) {
            $this->brands[ $b->name ] = $b;
        }
    }

    private function get_current_products() {
        $product = new Product();
        $products = $product->get_by_brand( self::BRAND_ID );

        $products_by_sku = array();
        foreach ( $products as $product ) {
            $products_by_sku[$product->sku] = $product;
        }
        return $products_by_sku;
    }

    private function get_category_id($category_name) {
        // TODO

        $gsr_category_name = isset( $this->category_map[$category_name] ) ? $this->category_map[$category_name] : null;

        if ( $gsr_category_name && isset( $this->categories_by_name[$gsr_category_name] ) ) {
            return $this->categories_by_name[$gsr_category_name];
        }

        return null;
    }

    public function run() {
        set_time_limit( 3600 );

        echo "Connecting...\n";
        $connection = mssql_connect( self::DB_URL, self::DB_USER, self::DB_PASS );
        mssql_select_db( self::DB_NAME, $connection );

        $page = 15;
        do {
            $start = $page * 10000;
            $end = ($page+1) * 10000;
            $page++;
            echo date('Y-m-d H.i.s') . " - SELECT * FROM stage WHERE id BETWEEN $start AND $end\n";

            $query = mssql_query("SELECT * FROM stage WHERE id BETWEEN $start AND $end");
            if (!$query) {
                echo "Something bad happened\n";
                return;
            }

            $i = 0;
            while (($row = mssql_fetch_object($query)) !== FALSE) {
                $this->process_row($row);
                $i++;
            }

            echo "Processed $i rows from $start to $end\n";
        } while ( $i != 0 );
        echo "Finished\n";

        mssql_close( $connection );
    }

    private function process_row( $row ) {
        echo "Getting $row->sku_code...\n";

        $brand = isset( $this->brands[ $row->vendor_name ] ) ? $this->brands[ $row->vendor_name ] : NULL;
        if ( !$brand ) {
            echo "Creating Brand {$row->vendor_name}...\n";
            $brand = new Brand();
            $brand->name = $row->vendor_name;
            $brand->slug = format::slug( $row->vendor_name );
            $brand->create();
            $this->brands[ $row->vendor_name ] = $brand;
        }

        echo "Brand #{$brand->id}\n";

        $product = new Product();
        $product_id = $product->get_var("SELECT product_id FROM products WHERE sku = '{$row->sku_code}' AND user_id_created = " . self::USER_ID);
        if ( $product_id ) {
            $product->get( $product_id );
        }

        if ( !$product->id ) {
            if ( $row->ecommerce != 'Y' ) {
                echo "New product, not for ecommerce. Skip\n";
                return;
            }

            echo "Creating new Product\n";
            $product->website_id = 0;  // Global Product
            $product->user_id_created = self::USER_ID;
            $product->publish_visibility = 'private';
            $product->status = 'in-stock';
            $product->create();

            // [Collection] [Color] [Category] - [Size Category]
            $product_name = "{$row->collection_name} {$row->vendor_primary_color} {$row->category} - {$row->size_category}";
            $product->sku = $row->sku_code;
            $product->name = ucwords( strtolower ( $product_name ) );
            $product->slug = format::slug( $product->name );

            // echo "Trying with Image " . $row->medium_image_filename . " <br>\n";
            if ( curl::check_file( $row->medium_image_filename ) ) {
                $product->add_images( array(
                    $row->medium_image_filename
                ) );
                $product->publish_visibility = 'RMI';
            }
        } else {
            echo "Product found {$product->id}<br>\n";
            $product->user_id_modified = self::USER_ID;
        }

        // Check if slug already exists
        $duplicated_slug = new Product();
        $duplicated_slug->get_by_slug( $product->slug );
        // If slug exists, append random number and check again
        while ( $duplicated_slug->id != null ) {
            $product->slug = str_replace( '---', '-', format::slug( $product->name ) ) . '-' . rand( 1000, 9999 );
            unset($duplicated_slug);
            $duplicated_slug = new Product();
            $duplicated_slug->get_by_slug( $product->slug );
        }
        unset($duplicated_slug);

        $product->description = "<p>{$row->description}</p>\n";

        $product->brand_id = $brand->id;
        $product->category_id = $this->get_category_id( $row->category );
        $product->industry_id = 1;  // Furniture
        $product->price = $row->vendor_cost;
        $product->price_min = $row->map_price;
        $product->publish_date = date('Y-m-d H:i:s');

        $specifications = array();
        if ( $row->width_1 )
            $specifications[] = array( 'Width', "{$row->width_1}'{$row->width_2}\"" );
        if ( $row->length_1 )
            $specifications[] = array( 'Length', "{$row->length_1}'{$row->length_2}\"" );
        if ( $row->shape_name )
            $specifications[] = array( 'Shape', "{$row->shape_name}" );
        if ( $row->background_color_name )
            $specifications[] = array( 'Background Color', "{$row->background_color_name}" );
        if ( $row->border_color_name )
            $specifications[] = array( 'Border', "{$row->border_color_name}" );
        if ( $row->type_name )
            $specifications[] = array( 'Type', "{$row->type_name}" );
        if ( $row->origin_name )
            $specifications[] = array( 'Origin', "{$row->origin_name}" );

        if ( $row->ecommerce != 'Y' ) {
            $product->publish_visibility = 'deleted';
        }

        $product->save();

        if ( !empty( $specifications ) ) {
            $product->delete_specifications();
            $product->add_specifications( $specifications );
        }

        unset($product);
        unset($specifications);
        unset($row);
    }

}