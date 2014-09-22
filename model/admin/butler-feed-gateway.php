<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 9/18/14
 * Time: 2:00 PM
 */

class ButlerFeedGateway extends ActiveRecordBase {

    const DATA_URL = 'http://supercat.supercatsolutions.com/bsc/products.json';
    const DATA_USER = 'kerry';
    const DATA_PASS = 'imagine';

    const IMAGE_URL = 'http://supercat.supercatsolutions.com/data/14/image/';

    const BRAND_ID = 120;
    const USER_ID = 2605;

    private $category_map = array(
        'AT' => 'Tables > Accent Tables',
        'CC' => 'Tables > Chairside Tables and Chests',
        'CT' => 'Tables > Chairside Tables and Chests',
        'FT' => 'Tables > Foyer Tables',
        'GT' => 'Tables > Game Tables',
        'NT' => 'Tables > Nesting Tables',
        'ST' => 'Tables > Serving Tables',
        'AR' => 'Jewelry Storage',
        'BED' => 'Bedroom > Beds > Complete Beds',
        'DMC' => 'Consoles and Chests > Dressers / Mirrors & Chests',
        'CHC' => 'Consoles and Chests > Chests / Cabinets',
        'CST' => 'Consoles and Chests > Console and Sofa Tables',
        'BC' => 'Office and Entertainment > Bookcases and Etageres',
        'ETG' => 'Office and Entertainment > Bookcases and Etageres',
        'AC' => 'Seating > Accent Chairs',
        'BCH' => 'Seating > Benches and Stools',
        'BST' => 'Seating > Bar Stools',
        'BQ' => 'Occasionals > Blanket / Quilt Racks',
        'CHT' => 'Occasionals > Costumers / Hall Trees',
        'CUR' => 'Wine Storage, Curios and More > Curios',
        'DSK' => 'Office and Entertainment > Writing Desks',
        'DS ' => 'Office and Entertainment > Secretaries',
        'EAS' => 'Occasionals > Easels',
        'EC' => 'Office and Entertainment > Entertainment Centers',
        'JC' => 'Jewelry Storage',
        'LR' => 'Occasionals > Luggage Racks',
        'MMS' => 'Occasionals > Magazine Racks',
        'MIR' => 'Occasionals > Mirrors',
        'PP' => 'Wine Storage, Curios and More > Pedestals and Planters',
        'SS' => 'Wine Storage, Curios and More > Step Stools',
        'US' => 'Occasionals > Umbrella Stands',
        'VT' => 'Occasionals > Valets',
        'VAN' => 'Wine Storage, Curios and More > Vanities',
        'WS' => 'Wine Storage, Curios and More > Wine Storage',
        'LMP' => "Hors D'oeuvres",
        'TT' => "Hors D'oeuvres",
        'OTA' => "Hors D'oeuvres"
    );

    private $categories_by_name = array();

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

    private function get_feed() {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, self::DATA_URL);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_USERPWD, self::DATA_USER.":".self::DATA_PASS);
        curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $content = curl_exec($c);
        curl_close($c);

        $content_lines = explode("\n", $content);

        $feed_elements = array();
        foreach ( $content_lines as $line ) {
            $feed_elements[] = json_decode( $line );
        }

        return $feed_elements;
    }

    private function get_category_id($feed_product) {
        $butler_category = $feed_product->category_code;
        $gsr_category_name = isset( $this->category_map[$butler_category] ) ? $this->category_map[$butler_category] : null;

        if ( $gsr_category_name && isset( $this->categories_by_name[$gsr_category_name] ) ) {
            return $this->categories_by_name[$gsr_category_name];
        }

        return null;
    }

    private function get_price_min($feed_product) {
        foreach ( $feed_product->custom_fields as $custom_field ) {
            if ( $custom_field[0] == 'MAP Price' ) {
                $min_price = $custom_field[1];
                // Parse "$1,234.56"
                $min_price = (float) str_replace( array('$', ','), '', $min_price );
                return $min_price;
            }
        }
        return 0;
    }

    public function run() {
        set_time_limit( 3600 );

        $current_product_list = $this->get_current_products();
        $feed_product_list = $this->get_feed();

        foreach ( $feed_product_list as $feed_product ) {
            echo "<hr>Getting $feed_product->item_number...<br>";

            /**
             * @var Product $product
             */
            $product = isset( $current_product_list[$feed_product->item_number] ) ?
                                $current_product_list[$feed_product->item_number] :
                                null;

            if ( !$product ) {
                echo "Creating new Product<br>";
                $product = new Product();
                $product->website_id = 0;  // Global Product
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'private';
                $product->status = 'in-stock';
                $product->create();

                echo "Trying with Image " . self::IMAGE_URL . $feed_product->image_file_name . " <br>";
                $image_url = self::IMAGE_URL . $feed_product->image_file_name;
                try {
                    $full_image_name = $product->upload_image( $image_url, $product->slug, 'furniture' );
                    $product->add_images( array( $full_image_name) );
                    $product->publish_visibility = 'public';
                } catch ( Exception $e ) { }
            } else {
                echo "Product found {$product->id}<br>";
                $product->user_id_modified = self::USER_ID;
            }

            $product->sku = $feed_product->item_number;
            $product->name = ucwords( strtolower ( $feed_product->plist_description ) );

            $product->slug = format::slug( $product->name );

            // Check if slug already exists
            $duplicated_slug = new Product();
            $duplicated_slug->get_by_slug( $product->slug );
            // If slug exists, append random number and check again
            while ( $duplicated_slug->id != null ) {
                $product->slug = str_replace( '---', '-', format::slug( $product->name ) ) . '-' . rand( 1000, 9999 );
                $duplicated_slug = new Product();
                $duplicated_slug->get_by_slug( $product->slug );
            }

            $product->description = "<p>{$feed_product->story}</p>";

            $product->brand_id = self::BRAND_ID;
            $product->category_id = $this->get_category_id( $feed_product );
            $product->industry_id = 1;  // Furniture

            $product->price = $feed_product->net_price;
            $product->price_min = $this->get_price_min( $feed_product );

            $specifications = array();
            $dimensions = explode( ', ', $feed_product->product_dimensions_in );
            foreach ( $dimensions as $d ) {
                if ( stripos($d, 'Diam.') !== false )
                    $specifications[] = array('Diam.', trim( str_replace( 'Diam.', '', $d ) ) );
                else if ( stripos($d, 'W') !== false )
                    $specifications[] = array('Width', trim( str_replace( 'W', '', $d ) ) );
                else if ( stripos($d, 'H') !== false )
                    $specifications[] = array('Height', trim( str_replace( 'H', '', $d ) ) );
                else if ( stripos($d, 'D') !== false )
                    $specifications[] = array('Depth', trim( str_replace( 'D', '', $d ) ) );
            }

            if ( $feed_product->deleted ) {
                $product->publish_visibility = 'deleted';
            }

            echo json_encode($product) . "<br>";
            echo json_encode($specifications) . "<br>";

            $product->save();

            if ( !empty( $specifications ) ) {
                $product->delete_specifications();
                $product->add_specifications( $specifications );
            }
            flush();
        }
        echo "Finished";

    }

}