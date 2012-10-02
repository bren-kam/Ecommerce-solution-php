<?php
/**
 * Handles All package feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class AshleyPackageProductFeedGateway extends ProductFeedGateway {
    const FTP_URL = 'ftp.ashleyfurniture.com';
    const IMAGE_URL = 'https://www.ashleydirect.com/graphics/';
    const USER_ID = 1477; // Ashley

    /**
     * Hold ashley items
     */
    protected $packages, $series, $package_templates;

    /**
     * Holds product
     * @var Product
     */
    protected $product;

    /**
     * Hold all the non existent groups
     * @var array
     */
    protected $non_existent_groups = array();

    /**
     * Hold the file to XML data from
     * @var string|null
     */
    protected $file = null;

    /**
     * Hold Ashley API
     * @var Ashley_API
     */
    protected $ashley;

    /**
     * Hold Ashley Products by sku
     * @var array
     */
    protected $ashley_products;

    /**
     * Hold the ashley brands
     */
    protected $brands = array(
        'Ashley' => 8
        , 'Signature Design' => 170
        , 'Benchcraft' => 8
        , 'Millennium' => 171
        , 'Room Solutions' => 8
    );

    /**
     * Hold the ashley categories
     */
    protected  $categories = array(
        'Accents' => 0
        , 'Stationary Upholstery' => 218
        , 'Motion Upholstery' => 348
        , 'Sectionals' => 226
        , 'Chairs' => 221
        , 'Stationary Leather' => 255
        , 'Recliners' => 222
        , 'Motion Leather' => 255
        , 'Dining' => 347
        , 'Master Bedroom' => 228
        , 'Metal Beds' => 685
        , 'Youth Bedroom' => 267
        , 'Top of Bed' => 463
        , 'Curios' => 434
        , 'Home Office' => 328
        , 'Lamps' => 194
        , 'Mattresses' => 0
        , 'Rugs' => 338
        , 'Occasional' => 382
        , 'Walls' => 336
        , 'Entertainment' => 335
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
        ini_set( 'max_execution_time', 600 ); // 10 minutes
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 600 );
        
        // Get libraries
        library('ashley-api/ashley-api');
        $this->ashley = new Ashley_API();
    }

    /**
     * Get Data from Ashley
     */
    protected function get_data() {
        // Get Ashley Products by SKU
        $this->ashley_products = $this->get_ashley_products_by_sku();

        // Get packages
        $this->packages = $this->ashley->get_packages();

		echo str_repeat( '&nspb;', 1000 );
		flush();

        // Get series
        $series_array = $this->ashley->get_series();

		echo str_repeat( '&nspb;', 1000 );
		flush();

        // Arrange series
        foreach ( $series_array as $sa ) {
            $this->series[(string)$sa->SeriesNo] = $sa;
        }

		echo str_repeat( '&nspb;', 1000 );
		flush();

        // Get Templates
        $package_template_array = $this->ashley->get_package_templates();

        // Arrange templates
        foreach ( $package_template_array as $pta ) {
            $this->package_templates[(string)$pta->TemplateId] = $pta;
        }
    }

    /**
     * Now process everything with the data we have
     */
    protected function process() {
        // Generate array of our items
		foreach ( $this->packages as $item ) {
			/***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            echo str_repeat( ' ', 50 );
            set_time_limit(30);
			flush();

            // Reset errors
            $this->reset_error();

            /***** CHECK PRODUCT *****/

            // Setup the variables to see if we should continue
            $image = (string) $item->Image;

            // Image can't be empty
			$this->check( !empty( $image ) );

            // If it has an error, don't continue
            if ( $this->has_error() )
                continue;

            /***** GET PRODUCT *****/

            // Get Product
			$product = $this->get_existing_product( (string) $item->PackageName );

            // Now we have the product
            if ( !$product instanceof Product ) {
                $product = new Product();
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'private';
                $product->create();

                // Set publish date
                $product->publish_date = dt::now();
            }

            /***** PREPARE PRODUCT DATA *****/

            $package_series = $this->series[(string)$item->SeriesNo];
            $template = $this->package_templates[(string)$item->TemplateId];
            $sku = (string) $item->PackageName;

            // Get the name -- which may be hard if the description is empty
			if ( empty( $template->Descr ) ) {
				$sku_pieces = explode( '/', $sku );
				$series = array_shift( $sku_pieces );

				$name_pieces = array();

				foreach ( $sku_pieces as $sp ) {
					if ( isset( $this->ashley_products[$series . $sp] ) ) {
						$name_piece = str_replace( (string) $item->SeriesName, '', $this->ashley_products[$series . $sp] );
					} elseif( isset( $this->ashley_products[$series . '-' . $sp] ) ) {
						$name_piece = str_replace( (string) $item->SeriesName, '', $this->ashley_products[$series . '-' . $sp] );
					} else {
						continue;
					}

					$name_pieces[] = preg_replace( '/^ - /', '', $name_piece );
				}

				$name = $item->SeriesName . ' - ' . implode( ', ', $name_pieces );
			} else {
				$name = $item->SeriesName . ' ' . $template->Descr;
			}

            // Will have to format this
            $style_description = trim( (string) $package_series->StyleDescription );

            // Create description
            $description = format::convert_characters( format::autop( format::unautop( '<p>' . $package_series->Description . "</p>\n\n<p>" . $package_series->Features . "</p>\n\n<p>" . $package_series->SeriesColor . "</p>\n\n<p>" . $style_description . '</p>' ) ) );

            // Set product specs
            if ( empty( $style_description ) ) {
                $product_specs = '';
            } else {
                $product_specs = 'Style Description`' . $style_description . '`0';
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

            // Get Category ID
            $category_id = $this->categories[(string)$package_series->Grouping];

            /***** ADD PRODUCT DATA *****/

            // Reset the product to being "not" identical
            $this->reset_identical();

            /** Add Category **/
            if ( $category_id != $product->category_id ) {
                $product->delete_categories();
                $product->add_category( $category_id );
            }

            // Set product data
            $product->name = $this->identical( $name, $product->name, 'name' );
            $product->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $product->slug, 'slug' );
            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->brand_id = $this->identical( $this->brands[(string)$package_series->Showroom], $product->brand_id, 'brand' );
            $product->description = $this->identical( $description, format::autop( format::unautop( $product->description ) ), 'description' );

            /** Product Specs are special */
            $product_specifications = explode( '|', $this->identical( $product_specs, $new_product_specifications, 'product-specifications' ) );

            $product_specifications_array = array();

            foreach ( $product_specifications as $ps ) {
                $product_specifications_array[] = explode( '`', $ps );
            }

            $product->product_specifications = serialize( $product_specifications_array );

            /***** ADD PRODUCT IMAGES *****/

            // Let's hope it's big!
			$image_url = self::IMAGE_URL . $image;

            // Setup images array
            $images = explode( '|', $product->images );

            if ( ( 0 == count( $images ) || empty( $images[0] ) ) && !empty( $image ) && curl::check_file( $image_url ) ) {
                /**
                 * @var string $industry
                 */
                $image_name = $this->upload_image( $image_url, $product->slug, $product->id, $industry );

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

			$product->update();

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
        // Report just to CTO
        $user = new User();
        $user->get(1); // Kerry Jones

        $subject = 'Ashley Packages Feed - ' . dt::now();

        $new_products = @implode( PHP_EOL, $this->new_products );

        $message = 'New Products: ' . count( $this->new_products ) . PHP_EOL;
        $message .= 'Skipped/Unadjusted Products: ' . count( $this->skipped ) . PHP_EOL;
        $message .= str_repeat( PHP_EOL, 2 );
        $message .= "List Of New Products:" . PHP_EOL . $new_products;
        $message .= str_repeat( PHP_EOL, 2 );
        $message .= "Groups We Don't Have:" . PHP_EOL . @implode( PHP_EOL, $this->non_existent_groups );

        fn::mail( $user->email, $subject, $message );

        // Send report to everyone else
        if( count( $this->new_products ) > 0 ) {
			$message = "-----New Products-----" . PHP_EOL . $new_products;

			fn::mail( 'kerry@greysuitretail.com, david@greysuitretail.com, rafferty@greysuitretail.com, chris@greysuitretail.com', 'Ashley Products - ' . dt::now(), $message );
		}
    }

    /**
     * Get ashley products by sku
     *
     * @return array
     */
    protected function get_ashley_products_by_sku() {
        return ar::assign_key( $this->get_results( "SELECT `sku`, `name` FROM `products` WHERE `user_id_created` = 353 AND `publish_visibility` = 'public'", PDO::FETCH_ASSOC ), 'sku', true );
    }
}
