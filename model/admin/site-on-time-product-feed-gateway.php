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
    protected  $brand_ids = NULL;

    /**
     * Hold objects for use in process
     */
    protected $products, $features, $assets;

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
        // Time how long we've been on this page
		$this->curl = new curl();
		$this->product = new Product();
		$this->file = new File();

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
        // Initiate product string
        $products_string = $non_existent_categories = '';

        // Any new products get al ink
        $links = array();
		
		foreach ( $this->products as $product ) {
            // Trick to make sure the page doesn't timeout or segfault
            echo str_repeat( ' ', 50 );
            set_time_limit(30);
			flush();

			// Get the item
            $product = $product->{'stdClass Object'};
			
            // Setup the variables to see if we should continue
			$name = trim( preg_replace( '/-+$/', '', $product->SeriesName . ' ' . $product->ModelDescription . ' - ' . $product->StandardColor ) );

            // Check to make sure we should continue
			if ( ' - ' == $name )
				continue;

            // Figure out the industry
            switch ( $product->MenuHeading ) {
                case 'Appliances':
                    $industry_id = 3;
					$industry = 'appliances';
                break;

                case 'Electronics':
                    $industry_id = 2;
					$industry = 'electronics';
                break;

                default:
                    continue;
                break;
            }

            // Increment product count
			$this->tick();

            // Setup all the variables
			$slug = str_replace( '---', '-', format::slug( $name ) );
            $category_name = $product->Category . ' > ' . $product->SubCategory;
            $product_features = $this->features[$product->ProductGroupID];
            $product_assets = $this->assets[$product->SKU];

			// Arrange the features so that they are always in the same order
			ksort( $product_features );

            // Add key features
            $item_description = "<strong>Features</strong>";
            $item_description .= "\n" . $product->KeyFeature1;
            $item_description .= "\n" . $product->KeyFeature2;
            $item_description .= "\n" . $product->KeyFeature3;
            $item_description .= "\n" . $product->KeyFeature4;
            $item_description .= "\n" . $product->KeyFeature5;

            // Add Dimensions
            if ( isset( $product_features['DIMENSIONS'] ) ) {
                $item_description .= "\n\n\n<strong>Dimensions</strong>";

                foreach ( $product_features['DIMENSIONS'] as $dimension ) {
                    $item_description .= "\n" . $dimension;
                }
            }

            // Add other items
            $item_description .= "\n\n\n<strong>Other</strong>";
            $item_description .= "\nColor: " . $product->StandardColor;
            $item_description .= "\nModel No: " . $product->StandardColor;

            // If they have a spec page
            if ( isset( $product_assets['SpecPage'] ) )
                $item_description .= "\n\n\n<a href='" . $product_assets['SpecPage'] . "' title='Product Specifications' target='_blank'>Click here to download the product specifications for this product.</a>";

            // If they have an energy guide
            if ( isset( $product_assets['EnergyGuide'] ) )
                $item_description .= "\n\n\n<a href='" . $product_assets['EnergyGuide'] . "' title='Energy Guide' target='_blank'>Click here to download the energy guide for this product.</a>";

            // Define the description as it needs to be
			$description = format::autop( format::unautop( '<p>' . $item_description . '</p>' ) );

            // Set SKU
			$sku = $product->SKU;

            // Set product specs
            if ( is_array( $product_features ) ) {
                $product_specs = '';
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

            // No reporting for weight and volume
			$weight = $volume = $price = $list_price = 0;

            // Get the brand ID -- create it if necessary
			$brand_id = $this->get_brand_id( $product->Brand );
            
            // Let's hope it's big!
			$image = $product->LargeImage;

            // Setup images array
			$images = array();

            // Add category
            $category_id = $this->category_translation[$category_name];

            if ( !$category_id ) {
                $non_existent_categories .= $product->Category . ' > ' . $product->SubCategory . "\n";
                continue;
            }

			////////////////////////////////////////////////
			// Get/Create the product
			if ( $this->get_existing_product( $sku ) ) {
				// Get the product
                $product = $this->get_existing_product( $sku );

                // Set variables
				$product_id = $product['product_id'];
				$publish_visibility = $product['publish_visibility'];
				$publish_date = $product['publish_date'];
				$product_images = explode( '|', $product['images'] );

				// Override data with existing data
                $name = $this->identical( $name, $product['name'], 'name' );

				if ( empty( $slug ) ) {
					$slug = $product['slug'];
				} elseif ( $slug != $product['slug'] ) {
					$slug = $this->unique_slug( $slug );

					if ( $slug != $product['slug'] ) {
						$identical = false;
					}
				}

				if( empty( $description ) ) {
					$description = format::autop( format::unautop( $product['description'] ) );
				} elseif ( $description != format::autop( format::unautop( $product['description'] ) ) ) {
					echo 'description';
					$identical = false;
				}

				$images = $product_images;

				if ( ( 0 == count( $images ) || empty( $images[0] ) ) && !empty( $image ) && curl::check_file( $image ) ) {
					$image_name = $this->upload_image( $image, $slug, $product_id, $industry );

					if ( !is_array( $images ) || !in_array( $image_name, $images ) ) {
						echo 'images';
						$identical = false;
						$images[] = $image_name;

						$this->p->add_product_images( $images, $product_id );
					}
				}
				
				if ( 0 == count( $images ) && 'private' != $publish_visibility ) {
					echo 'images';
					$identical = false;
					$publish_visibility = 'private';
				}
				
				$product_specifications = '';

				$product['product_specifications'] = unserialize( $product['product_specifications'] );
				if( is_array( $product['product_specifications'] ) )
				foreach( $product['product_specifications'] as $ps ) {
					if( !empty( $product_specifications ) )
						$product_specifications .= '|';

					$product_specifications .= $ps[0] . '`' . $ps[1] . '`' . $ps[2];
				}

				if( empty( $product_specs ) ) {
					$product_specs = $product_specifications;
				} elseif ( $product_specs != $product_specifications ) {
					echo 'specs';
					$identical = false;
				}

				if( empty( $brand_id ) ) {
					$brand_id = $product['brand_id'];
				} elseif ( $brand_id != $product['brand_id'] ) {
					echo 'brand';
					$identical = false;
				}

				if( empty( $product_status ) ) {
					$product_status = $product['status'];
					$links['updated-product'][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";
				} else {
					$links[$product_status][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";

					if ( $product_status != $product['status'] ) {
						echo 'status';
						$identical = false;
					}
				}

				if( empty( $weight ) ) {
					$weight = $product['weight'];
				} elseif ( $weight != $product['weight'] ) {
					echo 'weight';
					$identical = false;
				}

				if( empty( $volume ) ) {
					$volume = $product['volume'];
				} elseif ( $volume != $product['volume'] ) {
					echo 'volume';
					$identical = false;
				}

                if ( $category_id != $product['category_id'] ) {
                    echo 'category';
                    $identical = false;
                }

				// If everything is identical, we don't want to do anything
				if ( $identical ) {
					$skipped++;
					$products_string .= $name . "\n";
					continue;
				}
			} else {
				$product_id = $this->p->create( self::USER_ID );
			
				// Insert the feed product ID
				$this->_insert_feed_product_id( $product_id, $product->ProductID );

                // Make sure it's a unique slug
                $slug = $this->_unique_slug( $slug );

				// Upload image if it's not blank
				if ( !empty( $image ) && curl::check_file( $image ) ) {
                    $image_name = $this->upload_image( $image, $slug, $product_id, $industry );

					if ( !in_array( $image_name, $images ) )
						$images[] = $image_name;
				}

				$price = $list_price = 0;
				$publish_date = dt::date( 'Y-m-d' );

				$links['new-products'][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";

				// Add images
				$this->p->empty_product_images( $product_id );

				// Makes the images have the right sequence if they exist
				if ( is_array( $images ) ) {
					$j = 0;

					foreach ( $images as &$image ) {
						$image .= "|$j";
						$j++;
					}
				}

				$this->p->add_product_images( $images, $product_id );
                $products[$product->ProductID] = compact( 'name', 'slug', 'description', 'product-status', 'sku', 'price', 'list_price', 'product_specs', 'brand_id', 'publish_visibility', 'publish_date', 'product_id', 'weight', 'volume', 'images' );
			}

            if ( !isset( $publish_visibility ) || empty( $publish_visibility ) )
                $publish_visibility = 'public';

			// Update the product
			$this->p->update( $name, $slug, $description, 'in-stock', $sku, $price, $list_price, $product_specs, $brand_id, $industry_id, $publish_visibility, $publish_date, $product_id, $weight, $volume );

            // Empty the categories
            $this->p->empty_categories( $product_id );
			
			// Add any category
            if ( $category_id )
                $this->p->add_categories( $product_id, array( $category_id ) );

			$products_string .= $name . "\n";

			// We don't want to carry them around in the next loop
			unset( $images );

			if ( $i % 1000 == 0 ) {
				$message = memory_get_peak_usage(true) . "\n" . memory_get_usage(true) . "\n\n";

				foreach ( $links as $section => $link_array ) {
					$message .= ucwords( str_replace( '-', ' ', $section ) ) . ": " . count( $link_array ) . "\n";
				}

				$message .= "\n\nSkipped: " . $skipped;

				mail( 'tiamat2012@gmail.com', "Made it to $i", $message );
			}
		}
    }
    protected function send_report() {

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
