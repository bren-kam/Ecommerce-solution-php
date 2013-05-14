<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class SiteOnTimeProductFeedGateway extends ProductFeedGateway {
    const FTP_URL = 'http://www.cmicdata.com/datafeeds/product-data.php';
	const COMPANY_ID = 'B34FF55A-4FC8-4DF7-9FF3-91839248DC7A';
    const USER_ID = 1478; // SiteOnTime

    /**
     * Hold the brand IDs by name
     * @var array
     */
    protected $brand_ids = NULL;

    /**
     * Hold objects for use in process
     */
    protected $products, $features, $assets;

    /**
     * Hold all the non existent categories
     * @var array
     */
    protected $non_existent_categories = array();

    /**
     * Category translation array
     */
    protected $category_translation = array(
        'Trash Compactors > Trash Compactors' => 387 // Appliances > Trash Compactors
        , 'Cooktops > Electric' => 290 // Appliances > Ranges & Ovens
        , 'Cooktops > Electric: Downdraft' => 290 // Appliances > Ranges & Ovens
        , 'Cooktops > Gas' => 290 // Appliances > Ranges & Ovens
        , 'Cooktops > Gas: DownDraft' => 290 // Appliances > Ranges & Ovens
        , 'Dishwashers > Built-In' => 292 // Appliances > Dishwashers
        , 'Dishwashers > Dish Drawer' => 292 // Appliances > Dishwashers
        , 'Dishwashers > Portable' => 292 // Appliances > Dishwashers
        , 'Garbage Disposers > Disposers' => 654 // Appliances > Garbage Disposers
        , 'Dryers > Electric: Match Top Load' => 286 // Appliances > Dryers
        , 'Dryers > Gas: Match Top Load' => 286 // Appliances > Dryers
        , 'Dryers > Compact & Portable Dryers' => 286 // Appliances > Dryers
        , 'Dryers > Electric: Match Front Load' => 286 // Appliances > Dryers
        , 'Dryers > Gas: Match Front Load' => 286 // Appliances > Dryers
        , 'Blu Ray > Blu-Ray Players' => 659 // Electronics > BluRay
        , 'Freezers > All Freezer - Matches Refrigerator' => 289 // Appliances > Freezers
        , 'Freezers > Chest' => 289 // Appliances > Freezers
        , 'Freezers > Upright: No Defrost' => 289 // Appliances > Freezers
        , 'Freezers > Upright: Frost Free' => 289 // Appliances > Freezers
        , 'LCD TVs > LCD HDTV 11" - 19"' => 660 // Electronics > LCD TVs
        , 'LCD TVs > LCD HDTV 20" - 29"' => 660 // Electronics > LCD TVs
        , 'LCD TVs > LCD HDTV 30" - 39"' => 660 // Electronics > LCD TVs
        , 'LCD TVs > LCD HDTV 40" - 49"' => 660 // Electronics > LCD TVs
        , 'LCD TVs > LCD HDTV 50" - 59"' => 660 // Electronics > LCD TVs
        , 'LED TVs > LED 11" - 29"' => 661 // Electronics > LED TVs
        , 'LED TVs > LED 30" - 39"' => 661 // Electronics > LED TVs
        , 'LED TVs > LED 40" - 49"' => 661 // Electronics > LED TVs
        , 'LED TVs > LED 50" - 59"' => 661 // Electronics > LED TVs
        , 'LED TVs > LED 60" UP' => 661 // Electronics > LED TVs
        , 'Microwaves > Countertop' => 291 // Appliances > Microwaves
        , 'Microwaves > Over The Range' => 291 // Appliances > Microwaves
        , 'Microwaves > Built-In' => 291 // Appliances > Microwaves
        , 'Microwaves > Specialty Cooking' => 291 // Appliances > Microwaves
        , 'Wine & Beverage > Beer Dispensers' => 655// Appliances > Wine and beverage
        , 'Ovens > Electric: Single' => 290 // Appliances > Ranges & Ovens
        , 'Ovens > Electric: Double' => 290 // Appliances > Ranges & Ovens
        , 'Ovens > Electric: with Microwave' => 290 // Appliances > Ranges & Ovens
        , 'Ovens > Gas: Single' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Electric: Freestanding' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Electric: Slide-In' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Electric: Drop-In' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Gas: Freestanding' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Gas: Slide-In' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Dual Fuel Ranges' => 290 // Appliances > Ranges & Ovens
        , 'Ranges > Range Accessories' => 290 // Appliances > Ranges & Ovens
        , 'Refrigerators > Refrigerator: No Freezer' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Compact' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Under The Counter' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Top Freezer' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Bottom Freezer' => 288 // Appliances > Refrigerators
        , 'Refrigerators > French Door: Bottom Freezer' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Side x Side: No Dispenser' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Side x Side: with Dispenser' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Cabinet Depth: French Door' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Built-In: Side x Side' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Cabinet Depth: Bottom Freezer' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Cabinet Depth: SxS' => 288 // Appliances > Refrigerators
        , 'TV Accessories > TV Accessories' => 662 // Electronics > TV Acccessories
        , 'TV Video Combos > TV - DVD Combo' => 663 // Electronics > TV Video Combos
        , 'TV Furniture > TV Stands' => 664 // Electronics > TV Furniture
        , 'TV Furniture > TV Mounts' => 664 // Electronics > TV Furniture
        , 'Warming Drawers > Warming Drawers' => 656 // Appliances > Warming Drawers
        , 'Washers > Front Load' => 285 // Appliances > Washers
        , 'Washers > Top Load' => 285 // Appliances > Washers
        , 'Washers > High Efficiency Top Load Washers' => 285 // Appliances > Washers
        , 'Washers > Compact & Portable Washers' => 285 // Appliances > Washers
        , 'Washers > Stack Pair' => 285 // Appliances > Washers
        , 'Washers > Laundry Accessories' => 285 // Appliances > Washers
        , 'Wine & Beverage > Wine Storage' => 655 // Appliances > Wine and beverage
        , 'Wine & Beverage > Beverage Coolers' => 655 // Appliances > Wine and beverage
        , 'Plasma TVs > Plasma 50" - 59"' => 665 // Electronics > Plasma TVs
        , 'Plasma TVs > Plasma 40" - 49"' => 665 // Electronics > Plasma TVs
        , 'DLP TVs > DLP 60" -  69"' => 666 // Electronics > DLP TVs
        , 'DLP TVs > DLP 70" & UP' => 666 // Electronics > DLP TVs
        , 'Plasma TVs > Plasma 60" - 69' => 665 // Electronics > Plasma TVs
        , 'LCD TVs > LCD 60" & UP' => 660 // Electronics > LCD TVs
        , 'Cooktops > Electric Induction Cooktops' => 290 // Appliances > Ranges & Ovens
        , 'Refrigerators > Icemaker Kits' => 288 // Appliances > Refrigerators
        , 'Refrigerators > Refrigerator Accessories' => 288 // Appliances > Refrigerators
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
        // We need to up the limit
        set_time_limit(300);
        ini_set('memory_limit', '256M');
    }

    /**
     * Get Data from Site On Time
     */
    protected function get_data() {
        // Get products
        $arguments = http_build_query( array( 'cid' => self::COMPANY_ID ) );
        $this->products = json_decode( curl::get( self::FTP_URL . '?' . $arguments, 240 ) );
		
        // Get Features
        $arguments = http_build_query( array( 'cid' => self::COMPANY_ID, 'type' => 'features' ) );
        $product_features = json_decode( curl::get( self::FTP_URL . '?' . $arguments, 240 ) );
        
        // Organize features
        foreach ( $product_features as $pf ) {
            $f = $pf->{'stdClass Object'};

            $this->features[$f->ProductGroupID][$f->FeatureCategory][] = $f->Feature;
        }

        unset( $product_features );

        // Get Assets
        $arguments = http_build_query( array( 'cid' => self::COMPANY_ID, 'type' => 'assets' ) );
        $product_assets = json_decode( curl::get( self::FTP_URL . '?' . $arguments, 240 ) );

        // Organize Assets
        foreach ( $product_assets as $pa ) {
            $a = $pa->{'stdClass Object'};

            if ( !in_array( $a->AssetName, array( 'EnergyGuide', 'SpecPage' ) ) )
                continue;
			
            $this->assets[$a->SKU][$a->AssetName] = $a->AssetURL;
        }

        unset( $product_assets );
    }

    /**
     * Now process everything with the data we have
     */
    protected function process() {
		foreach ( $this->products as $product ) {
            /***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            echo str_repeat( ' ', 50 );
            set_time_limit(30);
			flush();

            // Reset errors
            $this->reset_error();

			// Get the item
            $sot_product = $product->{'stdClass Object'};

            /***** CHECK PRODUCT *****/

            // Setup the variables to see if we should continue
			$name = trim( preg_replace( '/-+$/', '', $sot_product->SeriesName . ' ' . $sot_product->ModelDescription . ' - ' . $sot_product->StandardColor ) );
            $category_name = $sot_product->Category . ' > ' . $sot_product->SubCategory;
            $category_id = $this->category_translation[$category_name];

            // Check to make sure we should continue
			$this->check( ' - ' != $name );

            if ( !$this->check( $category_id ) )
                $non_existent_categories[] = $sot_product->Category . ' > ' . $sot_product->SubCategory . "\n";

            $this->check( in_array( $sot_product->MenuHeading, array( 'Appliances', 'Electronics' ) ) );

            // If it has an error, don't continue
            if ( $this->has_error() )
                continue;

            /***** GET PRODUCT *****/

            // Get Product
			$product = $this->get_existing_product( $sot_product->SKU );

            // Now we have the product
            if ( !$product instanceof Product ) {
                $product = new Product();
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'public';
                $product->create();

                // Set publish date
                $product->publish_date = dt::now();
            }

            /***** PREPARE PRODUCT DATA *****/

            /** Industry **/
            switch ( $sot_product->MenuHeading ) {
                case 'Appliances':
                    $industry_id = 3;
					$industry = 'appliances';
                break;

                case 'Electronics':
                    $industry_id = 2;
					$industry = 'electronics';
                break;
            }

            /** Product description **/
            $product_features = $this->features[$sot_product->ProductGroupID];
            $product_assets = $this->assets[$sot_product->SKU];

			// Arrange the features so that they are always in the same order
			ksort( $product_features );

            // Add key features
            $item_description = "<strong>Features</strong>";
            $item_description .= "\n" . $sot_product->KeyFeature1;
            $item_description .= "\n" . $sot_product->KeyFeature2;
            $item_description .= "\n" . $sot_product->KeyFeature3;
            $item_description .= "\n" . $sot_product->KeyFeature4;
            $item_description .= "\n" . $sot_product->KeyFeature5;

            // Add Dimensions
            if ( isset( $product_features['DIMENSIONS'] ) ) {
                $item_description .= "\n\n\n<strong>Dimensions</strong>";

                foreach ( $product_features['DIMENSIONS'] as $dimension ) {
                    $item_description .= "\n" . $dimension;
                }
            }

            // Add other items
            $item_description .= "\n\n\n<strong>Other</strong>";
            $item_description .= "\nColor: " . $sot_product->StandardColor;
            $item_description .= "\nModel No: " . $sot_product->StandardColor;

            // If they have a spec page
            if ( isset( $product_assets['SpecPage'] ) )
                $item_description .= "\n\n\n<a href='" . $product_assets['SpecPage'] . "' title='Product Specifications' target='_blank'>Click here to download the product specifications for this product.</a>";

            // If they have an energy guide
            if ( isset( $product_assets['EnergyGuide'] ) )
                $item_description .= "\n\n\n<a href='" . $product_assets['EnergyGuide'] . "' title='Energy Guide' target='_blank'>Click here to download the energy guide for this product.</a>";

            /** Product Specifications **/
            $product_specs = '';

            // Set product specs
            if ( is_array( $product_features ) ) {
                $j = 0;

                foreach ( $product_features as $section => $section_features ) {
                    // Make sure we have a divider
                    if ( !empty( $product_specs ) )
                        $product_specs .= '|';

                    // Show all the section title on a line of its own
                    $product_specs .= $section . '``' . $j;
                    $j++;

                    // Show all the features, indented
                    foreach ( $section_features as $f ) {
                        $product_specs .= '|&amp;nbsp;`' . htmlentities( $f, ENT_QUOTES, 'UTF-8' ) . '`' . $j;
                        $j++;
                    }
                }
            }

            // Now get old specs
            $product_specifications = unserialize( $product->product_specifications );
            $new_product_specifications = '';

            if( is_array( $product_specifications ) )
            foreach( $product_specifications as $ps ) {
                if( !empty( $product_specifications ) )
                    $new_product_specifications .= '|';

                $new_product_specifications .= $ps[0] . '`' . $ps[1] . '`' . $ps[2];
            }

            /***** ADD PRODUCT DATA *****/

            // Reset the product to being "not" identical
            $this->reset_identical();

            /** Add Category **/
            $product->category_id = $this->identical( $category_id, $product->category_id, 'category_id' );

            /**
             * @var int $industry_id
             */
            $product->name = $this->identical( $name, $product->name, 'name' );
			$this->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $sot_product->slug, 'slug' );
            $product->sku = $this->identical( $sot_product->SKU, $product->sku, 'sku' );
            $product->weight = $this->identical( '', $product->weight, 'weight' );
			$product->brand_id = $this->identical( $this->get_brand_id( $sot_product->Brand ), $product->brand_id, 'brand' );
            $product->industry_id = $this->identical( $industry_id, $product->industry_id, 'industry' );
			$product->description = $this->identical( format::autop( format::unautop( '<p>' . $item_description . '</p>' ) ), format::autop( format::unautop( $product->description ) ), 'description' );

            /** Product Specs are special */
            $product_specifications = explode( '|', $this->identical( $product_specs, $new_product_specifications, 'product-specifications' ) );

            $product_specifications_array = array();

            foreach ( $product_specifications as $ps ) {
                $product_specifications_array[] = explode( '`', $ps );
            }

            $product->product_specifications = serialize( $product_specifications_array );

            /***** ADD PRODUCT IMAGES *****/

            // Let's hope it's big!
			$image = $sot_product->LargeImage;

            // Setup images array
            $images = explode( '|', $product->images );

            if ( ( 0 == count( $images ) || empty( $images[0] ) ) && !empty( $image ) && curl::check_file( $image ) ) {
                /**
                 * @var string $industry
                 */
                $image_name = $this->upload_image( $image, $product->slug, $product->id, $industry );

                if ( !is_array( $images ) || !in_array( $image_name, $images ) ) {
                    $this->not_identical[] = 'images';
                    $images[] = $image_name;

                    $product->add_images( $images );
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

            // Increment product count
	        $this->new_product( $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid={$product->id}\n" );

            // Add on to lists
            $this->existing_products[$product->sku] = $product;
		}
    }

    /**
     * Send a report
     */
    protected function send_report() {
        $user = new User();
        $user->get(1); // Kerry Jones

        $subject = 'SiteOnTime Feed - ' . dt::now();

        $message = 'New Products: ' . count( $this->new_products ) . PHP_EOL;
        $message .= 'Skipped/Unadjusted Products: ' . count( $this->skipped ) . PHP_EOL;
        $message .= str_repeat( PHP_EOL, 2 );
        $message .= 'List Of New Products:' . @implode( PHP_EOL, $this->new_products );
        $message .= str_repeat( PHP_EOL, 2 );
        $message .= "Categories We Don't Have:" . @implode( PHP_EOL, $this->non_existent_categories );

        fn::mail( $user->email, $subject, $message );
    }

    /**
     * Get a list of all the brands
     *
     * @param string $name
     * @return array
     */
    protected function get_brand_id( $name ) {
        // Make sure the brands are in place
        if ( !is_array( $this->brand_ids ) )
            $this->load_brands();

        // Check to make sure we have the brand
        if ( !isset( $this->brand_ids[$name] ) ) {
            // Create new brand
            $brand = new Brand();
            $brand->name = $name;
            $brand->create();

            $this->brand_ids[$name] = $brand->id;
        }

        return $this->brand_ids[$name];
    }

    /**
     * Load brands
     */
    protected function load_brands() {
        $brand = new Brand();
        $brands = $brand->get_all();

        /**
         * @var Brand $brand
         */
        foreach ( $brands as $brand ) {
            $this->brand_ids[$brand->name] = $brand->id;
        }
    }
}