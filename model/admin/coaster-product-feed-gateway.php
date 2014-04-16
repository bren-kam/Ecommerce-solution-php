<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class CoasterProductFeedGateway extends ProductFeedGateway {
    const USER_ID = 1915; // Coaster
    const BRAND_ID = 36;

    /**
     * Hold the item data
     * @var string|null
     */
    protected $items = null;

    /**
     * Hold CSV handler
     */
    protected $handle;

    /**
     * Hold the category code translation
     */
    protected $categories = array(
        'MATTRESS' => 165
        , 'BAR SET' => 347
        , 'BAR STOOL' => 142
        , 'TABLE' => 130
        , 'SIDE CHAIR' => 132
        , 'ARM CHAIR' => 131
        , 'CHINA' => 212
        , 'BUFFET' => 133
        , 'HUTCH' => 584
        , 'WINE RACK' => 136
        , 'BAR TABLE' => 144
        , 'GAME TABLE' => 727
        , 'BENCH' => 141
        , 'PARSON CHAIR' => 728
        , 'SERVER' => 726
        , '3 PACK' => 0
        , '5 PACK' => 0
        , 'PILLOW' => 597
        , 'KITCHEN ISLAND' => 730
        , 'ACCENT CHAIR' => 221
        , 'CURIO' => 422
        , 'CHINA CABINET' => 212
        , 'BED FRAMES' => 126
        , 'DRESSER' => 101
        , 'TV STAND/ARMOIRE' => 104
        , 'BED FRAME' => 126
        , 'TRUNDLE' => 581
        , 'DAY BED' => 589
        , 'FLOOR LAMP' => 194
        , 'TABLE LAMP' => 194
        , 'BED' => 439
        , 'NIGHTSTAND' => 105
        , 'MIRROR' => 102
        , 'CHEST' => 103
        , 'MEDIA CHEST' => 107
        , 'FUTON COVER' => 731
        , 'DESK' => 329
        , 'FUTON PAD' => 731
        , 'ARMOIRE' => 104
        , '8 DRAWER CHEST' => 103
        , 'COAT RACK' => 690
        , 'HEADBOARD ONLY' => 125
        , 'LOFT BUNK' => 688
        , 'LINGERIE CHEST' => 103
        , 'BUNK BED' => 617
        , 'FUTON FRAME' => 603
        , 'VANITIES' => 119
        , 'ACCENT TABLE' => 233
        , 'SERVING CART' => 746
        , 'STORAGE RACK' => 136
        , 'CANOPY' => 748
        , 'GLIDER' => 221
        , 'ROOM DIVDER' => 739
        , 'BOOKCASE' => 452
        , 'HEADBOARD/FOOTBOARD' => 439
        , 'SOFA BED' => 219
        , 'ADJUSTABLE BED' => 441
        , 'SOFA' => 219
        , 'OTTOMAN' => 229
        , 'CHAISE' => 249
        , 'CHAIR' => 221
        , 'LOUNGE CHAIR' => 221
        , 'JEWELRY ARMOIRE' => 104
        , 'CHEVAL MIRROR' => 102
        , 'STOOL' => 740
        , 'PLANT STAND' => 747
        , 'STEP STOOL' => 740
        , 'NESTING TABLES' => 233
        , 'END TABLE' => 233
        , 'COFFEE TABLE' => 231
        , 'SOFA TABLE' => 234
        , 'HALL TREE' => 690
        , 'DESK CHAIR' => 437
        , 'STORAGE' => 0
        , 'TOY CHEST' => 272
        , 'LOVE SEAT' => 220
        , 'OFFICE CHAIR' => 437
        , 'ROCKER' => 732
        , 'ACCENT CABINET' => 736
        , 'CABINET' => 736
        , 'STAIRWAY CHEST' => 733
        , 'KIDS CHAIR' => 279
        , '5 PCS SET' => 0
        , '3 PCS SET' => 0
        , 'MULTI TABLE' => 233
        , 'CEDAR CHEST' => 103
        , 'SECTIONAL' => 226
        , 'SLEEPER' => 425
        , 'WEDGE' => 695
        , 'ARMLESS CHAIR' => 695
        , 'RECLINER' => 222
        , 'TRAY TABLES' => 737
        , 'SECRETARY' => 329
        , 'LFT/RT PIER' => 336
        , 'THEATER SEATING' => 413
        , 'UTILITY CLOSET' => 745
        , 'CD/DVD STORAGE' => 622
        , 'BRIDGE' => 336
        , 'MAGAZINE RACK' => 738
        , 'PIE SHAPE TABLE' => 231
        , 'CHAIRSIDE TABLE' => 237
        , 'WORK STATION' => 0
        , 'FILE CABINET' => 330
        , 'VANITY STOOL' => 740
        , 'LAPTOP STAND' => 741
        , 'TOWEL RACK' => 742
        , 'SCREEN' => 739
        , 'CONSOLE TABLE' => 235
        , 'FIREPLACE' => 250
        , 'BOMBE CHEST' => 239
        , 'KITCHEN CART' => 730
        , 'VALET' => 734
        , 'CLOCK' => 196
        , 'PHONE STAND' => 735
        , 'SNACK TABLE' => 231
        , 'WALL ART' => 339
        , 'ACCENT PILLOW' => 743
        , 'ENTRY TABLE' => 734
        , 'RUGS' => 338
        , 'GLASS TOP' => 130
    );

    // Roomname conversion
    protected $roomnames = array(
        'B (BEDROOM)'           => 'bedroom'
        , 'D (DINING)'          => 'dining'
        , 'A (ACCESSORIES)'     => 'accents'
        , 'L (LIVING ROOM)'     => 'living-room'
        , 'H (HOME OFFICE)'     => 'home-office'
    );

    /**
     * Construct
     */
    public function __construct() {
        parent::__construct( self::USER_ID );
    }

    /**
     * Do the setup to get anything we need
     */
    protected function setup() {
        ini_set( 'max_execution_time', 1200 ); // 20 minutes
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 1200 );
		
        // Load excel reader
        $this->handle = fopen( '/gsr/systems/backend-testing/temp/product-master-2013.csv', "r" );
    }

    /**
     * Get Data from Ashley
     */
    protected function get_data() {
        // Nothing special needs to be done here
    }

    /**
     * Now process everything with the data we have
     */
    protected function process() {
        // Generate array of our items
        while( $item = fgetcsv( $this->handle ) ) {
            //fn::info( $item );exit;
            if ( 'Itemnumber' == $item[0] )
                continue;

            /***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            echo str_repeat( ' ', 50 );
            set_time_limit(30);
			flush();

            // Reset errors
            $this->reset_error();

            /***** GET PRODUCT *****/

            // Setup the variables to see if we should continue
			$sku = trim( $item[0] );

            $name = $item[1] . ' ';
            $name .= ( empty( $item[18] ) ) ? $item[22] : $item[18];
            $name = format::convert_characters( trim( $name ) );

            // Get Product
			$product = $this->get_existing_product( $sku );

            // Now we have the product
            if ( !$product instanceof Product ) {
                $product = new Product();
                $product->category_id = $this->categories[$item[7]];
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'private';
                $product->create();

                // Set publish date
                $product->publish_date = dt::now();

                // Increment product count
                $this->new_product( $name . format::convert_characters( "\nhttp://admin.greysuitretail.com/products/add-edit/?pid={$product->id}\n" ) );
            } else {
				continue;
			}

            /***** PREPARE PRODUCT DATA *****/

            // Now get old specs
            $item_specifications[] = array( 'Depth', $item[23] );
            $item_specifications[] = array( 'Width', $item[24] );
            $item_specifications[] = array( 'Height', $item[25] );

            /***** ADD PRODUCT DATA *****/

            // Reset the product to being "not" identical
            $this->reset_identical();

            $product->industry_id = 1;
            $product->name = $this->identical( $name, $product->name, 'name' );
            $product->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $product->slug, 'slug' );
            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( 'in-stock', $product->status, 'status' );
            $product->weight = $this->identical( $item[16], $product->weight, 'weight' );
            $product->brand_id = $this->identical( self::BRAND_ID, $product->brand_id, 'brand' );
            $product->description = $this->identical( format::convert_characters( format::autop( format::unautop( '<p>' . trim( $item[15] ) . "</p>" ) ) ), format::autop( format::unautop( $product->description ) ), 'description' );

            /***** ADD PRODUCT IMAGES *****/

            // Let's hope it's big!
			$image = preg_replace( '/[^0-9]/', '', $sku );
            $image_urls = array();
            $image_url = '';

            $image_urls[] = 'http://www.greysuitretail.com/coaster-images/' . $this->roomnames[$item[5]] . '/' . $image . '.jpg';
            $image_urls[] = 'http://www.greysuitretail.com/coaster-images/' . $this->roomnames[$item[5]] . '/' . $image . '-A.jpg';
            $image_urls[] = 'http://www.greysuitretail.com/coaster-images/' . $this->roomnames[$item[5]] . '/' . $image . '-B.jpg';

            if ( 'bedroom' == $this->roomnames[$item[5]] ) {
                $image_urls[] = 'http://www.greysuitretail.com/coaster-images/beds-and-other/' . $image . '.jpg';
                $image_urls[] = 'http://www.greysuitretail.com/coaster-images/beds-and-other/' . $image . '-A.jpg';
                $image_urls[] = 'http://www.greysuitretail.com/coaster-images/beds-and-other/' . $image . '-B.jpg';
            }

            // Setup images array
            $images = explode( '|', $product->images );

            if ( ( 0 == count( $images ) || empty( $images[0] ) ) && !empty( $image ) ) {
                foreach( $image_urls as $url ) {
                    if ( curl::check_file( $url ) ) {
                        $image_url = $url;
                        break;
                    }
                }

                if ( !empty( $image_url ) ) {
					$skip = false;
					try {
						$image_name = $this->upload_image( $image_url, $product->slug, $product->id, 'furniture' );
					} catch ( HelperException $e ) {
						$skip = true;
					}
					
					
                    if ( !$skip && ( !is_array( $images ) || !in_array( $image_name, $images ) ) ) {
                        $this->not_identical[] = 'images';
                        $images[] = $image_name;

                        $product->add_images( $images );
                    }
                }
            }

            // Change publish visibility to private if there are no images
            if ( 0 == count( $images ) && 'private' != $product->publish_visibility ) {
                $this->not_identical[] = 'publish_visibility';
                $product->publish_visibility = 'private';
            }

            /***** SKIP PRODUCT IF IDENTICAL *****/

            // If everything is identical, we don't want to do anything
            if ( $this->is_identical() ) {
                $this->skip( $name );
                continue;
            }

            /***** UPDATE PRODUCT *****/

			$product->save();

            // Add specs
            $product->delete_specifications();
            $product->add_specifications( $item_specifications );

            // Add on to lists
            $this->existing_products[$product->sku] = $product;
		}
    }

    /**
     * Send a report
     */
    protected function send_report() {
        // Report just to CTO
        $user = new User();
        $user->get(1); // Kerry Jones

        $subject = 'Coaster Feed - ' . dt::now();

        $new_products = @implode( PHP_EOL, $this->new_products );

        $message = 'New Products: ' . count( $this->new_products ) . PHP_EOL;
        $message .= 'Skipped/Unadjusted Products: ' . count( $this->skipped ) . PHP_EOL;
        $message .= str_repeat( PHP_EOL, 2 );
        $message .= "List Of New Products:" . PHP_EOL . $new_products;

        fn::mail( $user->email, $subject, $message );

        // Send report to everyone else
        if( count( $this->new_products ) > 0 ) {
			$message = "-----New Products-----" . PHP_EOL . $new_products;

			fn::mail( 'kerry@greysuitretail.com, david@greysuitretail.com, rafferty@greysuitretail.com, productmanager@greysuitretail.com', 'Coaster Products - ' . dt::now(), $message );
		}
    }

    /**
     * Set the existing products
     */
    protected function get_existing_products() {
        $products = $this->prepare(
            "SELECT `product_id`, `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `weight`, `volume`, `product_specifications`, `publish_visibility`, `publish_date` FROM `products` WHERE `user_id_created` = :user_id_created"
            , 'i'
            , array( ':user_id_created' => $this->user_id )
        )->get_results( PDO::FETCH_CLASS, 'Product' );

        /**
         * @var Product $product
         */
        foreach ( $products as $product ) {
            $this->existing_products[$product->sku] = $product;
        }
    }

    /**
     * Cleanup
     */
    public function cleanup() {
        $products = $this->prepare(
            "SELECT `product_id`, `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `weight`, `volume`, `product_specifications`, `publish_visibility`, `publish_date` FROM `products` WHERE `user_id_created` = :user_id_created LIMIT 10000"
            , 'i'
            , array( ':user_id_created' => $this->user_id )
        )->get_results( PDO::FETCH_CLASS, 'Product' );

        library('aws/sdk.class');
        $s3 = new AmazonS3( array( 'key' => Config::key('aws-access-key'), 'secret' => Config::key('aws-secret-key') ) );
        $s3->debug_mode = true;
        $bucket = 'furniture.retailcatalog.us';

        /**
         * @var Product $product
         **/
        foreach ( $products as $product ) {
            if ( !$product->id )
                continue;
			
            $folder = str_replace( '/', '\/', 'products/' . $product->id );
            $response = $s3->delete_all_objects( $bucket, "/^$folder\//" );

            if ( !$response ) {
                echo $product->id;
                break;
            }

            $product->delete_images();
            $this->query( "DELETE FROM `products` WHERE `product_id` = $product->id LIMIT 1" );
        }
    }
}
