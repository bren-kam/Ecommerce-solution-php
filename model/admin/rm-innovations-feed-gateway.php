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
    const DB_PASS = 'YzdaXPH8!21';
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
        $query = mssql_query( "SELECT * FROM stage" );
        if ( !$query ) {
            echo "Something bad happened\n";
            return;
        }

        while ( ( $row = mssql_fetch_object( $query ) ) !== FALSE ) {
            $this->process_row( $row );
        }

        echo "Finished\n";

        mssql_close( $connection );
    }

    private function process_row( $row ) {
        echo "<hr>Getting $row->sku_code...<br>\n";

        /**
         * @var Product $product
         */
        $product = isset( $this->current_product_list[$row->sku_code] ) ?
            $this->current_product_list[$row->sku_code] :
            null;

        if ( !$product ) {
            if ( $row->ecommerce != 'Y' ) {
                return;
            }

            echo "Creating new Product<br>";
            $product = new Product();
            $product->website_id = 0;  // Global Product
            $product->user_id_created = self::USER_ID;
            $product->publish_visibility = 'private';
            $product->status = 'in-stock';
            $product->create();

            $product_name = "{$row->vendor_name} {$row->collection_name} {$row->type_name} {$row->sku_code}";
            $product->sku = $row->sku_code;
            $product->name = ucwords( strtolower ( $product_name ) );
            $product->slug = format::slug( $product->name );

            echo "Trying with Image " . $row->medium_image_filename . " <br>\n";
            if ( curl::check_file( $row->medium_image_filename ) ) {
                $product->add_images( array(
                    $row->medium_image_filename
                ) );
                $product->publish_visibility = 'public';
            }
        } else {
            echo "Product found {$product->id}<br>\n";
            $product->user_id_modified = self::USER_ID;
        }

        // Check if slug already exists
        $duplicated_slug = new Product();
        $duplicated_slug->get_by_slug( $product->slug );
        // If slug exists, append random number and check again
        while (     $duplicated_slug->id != null ) {
            $product->slug = str_replace( '---', '-', format::slug( $product->name ) ) . '-' . rand( 1000, 9999 );
            $duplicated_slug = new Product();
            $duplicated_slug->get_by_slug( $product->slug );
        }

        $product->description = "<p>{$row->description}</p>\n";

        $product->brand_id = self::BRAND_ID;
        $product->category_id = $this->get_category_id( $row->category );
        $product->industry_id = 1;  // Furniture
        $product->price = $row->vendor_cost;
        $product->price_min = $row->map_price;
        $product->publish_date = date('Y-m-d H:i:s');

        $specifications = array();
        $specifications[] = array( 'Width', "{$row->width_1}'{$row->width_2}\"" );
        $specifications[] = array( 'Depth', "{$row->length_1}'{$row->length_2}\"" );

        if ( $row->ecommerce != 'Y' ) {
            $product->publish_visibility = 'deleted';
        }

        echo json_encode($product) . "<br>\n";
        echo json_encode($specifications) . "<br>\n";

        $product->save();

        if ( !empty( $specifications ) ) {
            $product->delete_specifications();
            $product->add_specifications( $specifications );
        }

        flush();
    }

}