<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class ButlerFeedGateway extends ProductFeedGateway {
    const USER_ID = 2605; // Butler
    const BRAND_ID = 120;// Butler
    const FILE_PATH = 'butler.xls';
    const IMAGE_BASE = 'http://butlerspecialtyfurniture.net/images/';

    /**
     * Hold items and groups
     */
    protected $items, $groups;

    /**
     * Hold all the non existent groups
     * @var array
     */
    protected $non_existent_groups = array();

    /**
     * Hold the excel file reader
     * @var Excel_Reader
     */
    protected $reader;

    /**
     * Hold the category code translation
     */
    protected $categories = array(
        'CORNER BAKER\'S RACK' => 135 // Dining Room > Bakers Racks
        , 'OVAL MIRROR' => 202 // Accessories > Mirrors
        , 'SIDE TABLE' => 233 // Occasional > End Tables
        , 'ROUND END TABLE' => 233 // Occasional > End Tables
        , 'DRUM TABLE' => 237 // Occasional > Chair Side Tables
        , 'DEMILUNE CONSOLE TABLE' => 130 // Dining Room Tables
        , 'ENGLISH SETTEE' => 234 // Occasional > Sofa Tables
        , 'CLOCK COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'GAME TABLE' => 727 // Dining Room > Game Tables
        , 'SIDE CHAIR CURIO' => 434 // Accessories > Curio
        , 'ACCENT TABLE' => 233 // Occasional > End Tables
        , 'CLOVER LEAF PEDESTAL TABLE' => 237 // Occasional > Chair Side Tables
        , 'ROUND TABLE' => 237 // Occasional > Chair Side Tables
        , 'CLOVER PEDESTAL TABLE' => 237 // Occasional > Chair Side Tables
        , 'CREDENZA' => 605 // Home Office > Credenza
        , 'OVAL SIDE CHEST' => 236 // Occasional > Chair Side Chests
        , 'ROUND PEDESTAL TABLE' => 237 // Occasional > Chair Side Tables
        , 'OVAL SIDE TABLE' => 237 // Occasional > Chair Side Tables
        , 'BARREL TABLE' => 237 // Occasional > Chair Side Tables
        , 'OVAL ACCENT TABLE' => 237 // Occasional > Chair Side Tables
        , 'ACCENT HALL TABLE' => 237 // Occasional > Chair Side Tables
        , 'BOMBE TRUNK TABLE' => 233 // Occasional > End Tables
        , 'BOMBE COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'BENCH' => 443 // Occasional > Benches
        , 'NESTING TABLES' => 237 // Occasional > Chair Side Tables
        , 'FOYER TABLE' => 234 // Occasional > Sofa Tables
        , 'TRUNK TABLE' => 233 // Occasional > End Tables
        , 'DUFFEL TABLE' => 233 // Occasional > End Tables
        , 'CONSOLE TABLE' => 130 // Dining Room Tables
        , 'WRITING DESK' => 329 // Home Office > Home Office Desks
        , 'DESK' => 329 // Home Office > Home Office Desks
        , 'CONSOLE CABINET' => 235 // Occasional > Consoles
        , 'METAL CONSOLE' => 235 // Occasional > Consoles
        , 'DEMILUNE DESK' => 329 // Home Office > Home Office Desks
        , 'VANITY' => 119 // Bedroom > Vanities
        , 'FOYER TABLE (ITEM SHIPS IN TWO CARTONS)' => 234 // Occasional > Sofa Tables
        , 'CHEST' => 103 // Bedroom > Chests
        , 'SOFA/CONSOLE TABLE' => 130 // Dining Room Tables
        , 'MULTI-GAME CARD TABLE' => 727 // Dining Room > Game Tables
        , 'VALET' => 734 // Accessories > Valet
        , 'OTTOMAN' => 229 // Living Room  > Ottomans
        , 'PEDESTAL' => 464 // Occasional > Pedestal
        , 'PLANTER' => 747 // Accessories > Plant Stands
        , 'SCATTER TABLE' => 233 // Occasional > End Tables
        , 'STEP STOOL' => 756 // Other
        , 'JARDINIERE' => 747 // Accessories > Plant Stands
        , 'COSTUMER (ITEM SHIPS IN TWO CARTONS)' => -1 // None
        , 'COSTUMER' => 756 // Other
        , 'BLANKET STAND' => 756 // Other
        , 'MARTINI TABLE' => 231 // Occasional > Cocktail Tables
        , 'PEDESTAL CABINET' => 657 // Occasional > Cabinet
        , 'DEMILUNE BENCH' => 443 // Occasional > Benches
        , 'FILE CHEST' => 339 // Home Office > Home Office File Cabinets and Carts
        , 'CONSOLE CHEST' => 103 // Bedroom > Chests
        , 'END TABLE' => 233 // Occasional > End Tables
        , 'COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'TALL CHEST' => 103 // Bedroom > Chests
        , 'TRUNK COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'DEMILUNE CHEST' => 103 // Bedroom > Chests
        , 'TALL DOOR CHEST' => 103 // Bedroom > Chests
        , 'DOOR CHEST' => 103 // Bedroom > Chests
        , 'DRAWER CHEST' => 103 // Bedroom > Chests
        , 'LAMP TABLE' => 233 // Occasional > End Tables
        , 'STORAGE TABLE' => 130 // Dining Room > Tables
        , 'MAGAZINE BASKET' => 738 // Occasional > Magazine Racks
        , 'BAR STOOL' => 142 // Dining Room > Bar Stools
        , 'ACCENT CHEST' => 103 // Bedroom > Chests
        , 'PEDESTAL TABLE' => 237 // Occasional > Chair Side Tables
        , 'CHAIRSIDE CHEST' => 236 // Occasional > Chair Side Chests
        , 'STOOL' => 142 // Dining Room > Bar Stools
        , 'LUGGAGE RACK' => 756 // Other
        , 'MAGAZINE TABLE' => 233 // Occasional > End Tables
        , 'BUNCHING TABLE' => 233 // Occasional > End Tables
        , 'BUNCHING OTTOMAN' => 229 // Living Room  > Ottomans
        , 'ETAGERE' => 440 // Occasional > Etagere
        , 'HALL/PUB TABLE' => 144 // Dining Room > Pub Tables
        , 'MIRROR OTTOMAN' => 229 // Living Room  > Ottomans
        , 'VANITY SEAT' => 740 // Occasional > Vanity Stools
        , 'DISPLAY CONSOLE' => 235 // Occasional > Consoles
        , 'STORAGE BENCH' => 443 // Occasional > Benches
        , 'CONSOLE/SERVER TABLE' => 130 // Dining Room Tables
        , 'VANITY STOOL' => 740 // Occasional > Vanity Stools
        , 'CORNER ETAGERE' => 440 // Occasional > Etagere
        , 'BUNCHING CUBE' => 237 // Occasional > Chair Side Tables
        , 'STORAGE CHEST' => 103 // Bedroom > Chests
        , 'STORAGE PEDESTAL' => 464 // Occasional > Pedestal
        , 'ROUND ACCENT TABLE' => 233 // Occasional > End Tables
        , 'SILVER CHEST' => 103 // Bedroom > Chests
        , 'LADIES WRITING DESK' => 329 // Home Office > Home Office Desks
        , 'TRAY END TABLE' => 233 // Occasional > End Tables
        , 'CURIO TABLE' => 233 // Occasional > End Tables
        , 'STEP TABLE' => 233 // Occasional > End Tables
        , 'DEMILUNE CONSOLE' => 130 // Dining Room Tables
        , 'LIBRARY STAND' => 606 // Home Office > Library Table
        , 'TEA TABLE' => 233 // Occasional > End Tables
        , '3-TIER CONSOLE TABLE' => 130 // Dining Room Tables
        , 'MOBILE SERVER' => 133 // Dining Room > Buffets
        , 'BOOK TABLE' => 606 // Home Office > Library Table
        , 'RED COCKTAIL TRUNK' => 103 // Bedroom > Chests
        , 'BLACK COCKTAIL TRUNK' => 103 // Bedroom > Chests
        , 'PEMBROKE TABLE' => 233 // Occasional > End Tables
        , 'OVAL DRUM TABLE' => 237 // Occasional > Chair Side Tables
        , 'OVAL COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'BOOKCASE' => 424 // Accessories > Bookcase
        , 'PEDESTAL CONSOLE TABLE' => 130 // Dining Room Tables
        , 'TEA SERVER' => 756 // Other
        , 'SECRETARY (ITEM SHIPS IN TWO CARTONS)' => 329 // Home Office > Home Office Desks
        , 'CD/DVD STORAGE CHEST' => 107 // Bedroom > Media Chests
        , 'HALL TABLE' => 235 // Occasional > Sofa Tables
        , 'ENTERTAINMENT CENTER' => 335 // Home Entertainment > Entertainment Centers
        , 'DEMILUNE HUTCH' => 583 // Home Office > Hutch
        , 'OVAL STORAGE BOX' => 756 // Other
        , 'BUFFET' => 133 // Dining Room > Buffets
        , 'ROLLING HORSE STATUE' => 756 // Other
        , 'HORSE STATUE' => 756 // Other
        , 'PUB TABLE' => 144 // Dining Room > Pub Tables
        , 'JEWELRY BOX' => 115 // Bedroom > Jewelry Chests
        , 'WALL MIRROR' => 202 // Accessories > Mirrors
        , 'SIDEBOARD' => 133 // Dining Room > Buffets
        , 'FLOOR-STANDING MIRROR' => 202 // Accessories > Mirrors
        , 'SIDE CHAIR' => 132 // Dining Room > Side Chairs
        , 'DISPLAY CABINET' => 657 // Occasional > Cabinet
        , 'ENTRY TABLE' => 234 // Occasional > Sofa Tables
        , 'ACCENT CABINET' => 736 // Occasional > Accent Cabinets
        , 'VALET STAND' => 734 // Accessories > Valet
        , 'CHAIRSIDE TABLE' => 237 // Occasional > Chair Side Tables
        , 'MAGAZINE RACK' => 738 // Occasional > Magazine Racks
        , 'LEATHER STOOL' => 560 // Leather  > Leather Barstools
        , 'UMBRELLA STAND' => 756 // Other
        , 'CHEVAL MIRROR' => 114 // Bedroom > Cheval Mirrors
        , 'WINE RACK' => replace // replace
        , 'WALL CURIO' => replace // replace
        , 'PEDESTAL PLANT STAND' => replace // replace
        , 'BLANKET RACK' => replace // replace
        , 'BUNCHING ACCENT TABLE' => 233 // Occasional > End Tables
        , 'TRAY TABLE' => replace // replace
        , 'ARM CHAIR' => replace // replace
        , 'REVOLVING BAR STOOL' => 142 // Dining Room > Bar Stools
        , 'NEST OF TABLES' => replace // replace
        , 'CONSOLE/SOFA TABLE' => 234 // Occasional > Sofa Tables
        , 'ALMIRAH' => replace // replace
        , 'FLIP-TOP CONSOLE TABLE' => 130 // Dining Room Tables
        , 'CORNER CABINET' => 334 // Home Entertainment > Corner Cabinets
        , 'SECRETARY' => 329 // Home Office > Home Office Desks
        , 'LAPTOP DESK' => 329 // Home Office > Home Office Desks
        , 'OCTAGONAL ACCENT TABLE' => 233 // Occasional > End Tables
        , 'TIERED ACCENT TABLE' => 233 // Occasional > End Tables
        , 'STORAGE DRUM TABLE' => 237 // Occasional > Chair Side Tables
        , 'BUTLER TABLE' => replace // replace
        , 'NESTING COCKTAIL TABLES' => 231 // Occasional > Cocktail Tables
        , 'PEDESTAL ACCENT TABLE' => 233 // Occasional > End Tables
        , 'PUB GAME TABLE' => 144 // Dining Room > Pub Tables
        , 'COCKTAIL GAME TABLE' => 231 // Occasional > Cocktail Tables
        , 'FOLDING TABLE' => replace // replace
        , 'ANCHOR TABLE' => replace // replace
        , 'TABLE' => replace // replace
        , 'DROP-LEAF TABLE' => replace // replace
        , 'STANDING VANITY MIRROR' => 202 // Accessories > Mirrors
        , 'WINE CONSOLE' => 235 // Occasional > Consoles
        , 'STORAGE CABINET' => 626 // Bedroom > Storage Cabinet
        , 'ENTERTAINMENT CONSOLE' => 235 // Occasional > Consoles
        , 'WINE STAND' => replace // replace
        , 'BUNCHING SIDE TABLE' => replace // replace
        , 'KIDNEY-SHAPED TABLE' => replace // replace
        , 'TRAY COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'IRON BAR STOOL' => 142 // Dining Room > Bar Stools
        , 'STORAGE BASKET SET' => replace // replace
        , 'SWIVEL CHAIR' => replace // replace
        , 'PEDESTAL CHEST' => 103 // Bedroom > Chests
        , 'PLANT STAND' => replace // replace
        , 'STORAGE TRUNK' => 103 // Bedroom > Chests
        , 'ACCENT CHAIR' => replace // replace
        , 'CANDLE SCONCE' => replace // replace
        , 'MOVIE PROJECTOR' => replace // replace
        , 'TABLETOP CHEVAL MIRROR' => 114 // Bedroom > Cheval Mirrors
        , 'CANDLE HOLDER' => replace // replace
        , 'AIRPLANE CLOCK' => replace // replace
        , 'BUNCHING COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'BAR CABINET' => 325 // Dining Room > Storage Cabinets
        , 'WINE CABINET' => 325 // Dining Room > Storage Cabinets
        , 'STANDING PROPELLER' => replace // replace
        , 'VASE' => replace // replace
        , 'VASE SET' => replace // replace
        , 'WALL PLATTER' => replace // replace
        , 'FLOOR VASE' => replace // replace
        , 'FLOOR VASE SET' => replace // replace
        , 'STORAGE CASE' => replace // replace
        , 'TISSUE BOX' => replace // replace
        , 'STORAGE BASKET' => replace // replace
        , 'STORAGE BIN' => replace // replace
        , 'STORAGE TRUNK SET' => 103 // Bedroom > Chests
        , 'ANGEL FIGURINE' => replace // replace
        , 'CANDLE HOLDER SET' => replace // replace
        , 'FOLDING WINE RACK' => replace // replace
        , 'SERVING TRAY' => replace // replace
        , 'STORAGE BOX' => replace // replace
        , 'PICTURE FRAME' => replace // replace
        , 'TELEPHONE BENCH' => replace // replace
        , 'WAGON COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'DRAFTING CHAIR' => replace // replace
        , 'MOROCCAN TRAY TABLE' => replace // replace
        , 'MANGDAR TRAY TABLE' => replace // replace
        , 'PEDESTAL STAND' => 464 // Occasional > Pedestal
        , 'REVOLVING STOOL' => 142 // Dining Room > Bar Stools
        , 'JEWELRY CHEST' => 115 // Bedroom > Jewelry Chests
        , 'DROP-LEAF HALL TABLE' => 235 // Occasional > Sofa Tables
        , 'DROP-LEAF ACCENT TABLE' => 233 // Occasional > End Tables
        , 'NESTING OTTOMANS' => replace // replace
        , 'PARSONS CHAIR' => replace // replace
        , 'BOX ON STAND' => replace // replace
        , 'LOW BOY CONSOLE' => 235 // Occasional > Consoles
        , 'MIRROR' => 202 // Accessories > Mirrors
        , 'EASEL' => replace // replace
        , 'HEXAGONAL GAME TABLE' => replace // replace
        , 'WINE STORAGE CABINET (ITEM SHIPS IN TWO CARTONS)' => 325 // Dining Room > Storage Cabinets
        , 'BOOKCASE CONSOLE' => 235 // Occasional > Consoles
        , 'LOW BOOKCASE' => 424 // Accessories > Bookcase
        , 'GAME PIECES' => replace // replace
        , 'DRUM COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'TROLLEY BUFFET' => 133 // Dining Room > Buffets
        , 'NESTING PLANT STANDS' => replace // replace
        , 'FOLDING SIDE TABLE' => replace // replace
        , 'BAR CART' => replace // replace
        , 'FIGURINE' => replace // replace
        , 'DUCK FIGURINE' => replace // replace
        , 'ROCKING HORSE' => replace // replace
        , 'KEY BOX' => replace // replace
        , 'STORAGE CONTAINER' => replace // replace
        , 'DISPLAY SHELF' => replace // replace
        , 'JEWELRY CASE' => 115 // Bedroom > Jewelry Chests
        , 'STORAGE OTTOMAN' => replace // replace
        , 'BUNCHING CHESS TABLE' => replace // replace
        , 'ELEPHANT ACCENT TABLE' => 233 // Occasional > End Tables
        , 'BOMBE CHEST' => 103 // Bedroom > Chests
        , 'SIDE CHEST' => 103 // Bedroom > Chests
        , 'CHAMPAGNE BUCKET' => replace // replace
        , 'MIRRORED PEDESTAL' => 464 // Occasional > Pedestal
        , 'LIFT-TOP CHEST' => 103 // Bedroom > Chests
        , 'OCTAGONAL COCKTAIL TABLE' => 231 // Occasional > Cocktail Tables
        , 'ROUND SIDE TABLE' => replace // replace
        , 'OCTAGON ACCENT TABLE' => 233 // Occasional > End Tables
        , 'MAGAZINE STAND' => replace // replace
        , 'MISSION HALL TREE (ITEM SHIPS IN TWO CARTONS)' => replace // replace
        , 'TIERED SIDE TABLE' => replace // replace
        , 'MOBILE TRAY TABLE' => replace // replace
        , 'CORNER CURIO CABINET' => 422 // Dining Room > Curio Cabinets
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
		ini_set( 'memory_limit', '1024M' );
		set_time_limit( 1200 );

        // Load excel reader
        library('Excel_Reader/Excel_Reader');
        $this->reader = new Excel_Reader();
        // Set the basics and then read in the rows

        $this->reader->setOutputEncoding('ASCII');
    }

    /**
     * Get Data from Ashley
     */
    protected function get_data() {
        // Load the file
        $this->reader->read( ABS_PATH . '../' . self::FILE_PATH );
        $this->items = format::stripslashes_deep( array_slice( $this->reader->sheets[0]['cells'], 3 ) );
    }

    /**
     * Now process everything with the data we have
     */
    protected function process() {
        $categories = array();
        // Generate array of our items
		foreach( $this->items as $item ) {
            $categories[] = $item[4];
            continue;
			/***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            echo 'BF: ' . $item[3];
            set_time_limit(30);
			flush();

            // Reset errors
            $this->reset_error();

            // Setup the variables to see if we should continue
			$sku = $item[3];

            /***** GET PRODUCT *****/

            // Get Product
			$product = $this->get_existing_product( $sku );

            $name = ( isset( $item[8] ) ) ? $item[8] : $item[5] . ' ' . $item[19] . ' ' . $item[4];
            $name = ucwords( strtolower( $name ) );

            // Now we have the product
            if ( !$product instanceof Product ) {
				$new_product = true;
                $product = new Product();
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'public';
                $product->create();

                // Set publish date
                $product->publish_date = dt::now();

                // Increment product count
                $this->new_product( $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid={$product->id}\n" );
            } else {
                $new_product = false;
				$product->user_id_modified = self::USER_ID;
			}

            /***** PREPARE PRODUCT DATA *****/

            // Now get old specs
            $product_specifications = unserialize( $product->product_specifications );
            $product_specifications_string = '';

            if( is_array( $product_specifications ) )
            foreach( $product_specifications as $ps ) {
                if( !empty( $product_specifications ) )
                    $product_specifications_string .= '|';

                $product_specifications_string .= $ps[0] . '`' . $ps[1] . '`' . $ps[2];
            }

            $count = isset( $ps[2] ) ? $ps[2] : 0;

            if ( !empty( $item[49] ) ) {
                $new_product_specifications = '|Width`' . round( $item[49], 2 ) . ' inches`' . ( $count + 1 );
                $new_product_specifications .= '|Depth`' . round( $item[50], 2 ) . ' inches`' . ( $count + 2 );
                $new_product_specifications .= '|Height`' . round( $item[51], 2 ) . ' inches`' . ( $count + 3 );
            } else {
                $new_product_specifications_array = explode( ',' , $item[52] );
                $i = 0;
                $new_product_specifications = '';

                foreach ( $new_product_specifications_array as $new_product_spec ) {
                    $i++;

                    if( !empty( $new_product_specifications ) )
                        $new_product_specifications .= '|';

                    $new_product_specifications .= '`' . trim( $new_product_spec ) . '`' . $i;
                }
            }

            /***** ADD PRODUCT DATA *****/

            // Reset the product to being "not" identical
            $this->reset_identical();

            $product->industry_id = 1;

            // Ticket 17005 said to no longer change these.
			if ( $new_product || empty( $product->slug ) ) {
				$product->name = $name;
				$product->slug = str_replace( '---', '-', format::slug( $name ) );
			}

            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( 'in-stock', $product->status, 'status' );
            $product->brand_id = self::BRAND_ID;
            $product->description = $this->identical( format::convert_characters( format::autop( format::unautop( '<p>' . $item[56] . '</p>' ) ) ), format::autop( format::unautop( $product->description ) ), 'description' );

            /** Product Specs are special */
            $product_specifications = explode( '|', $this->identical( $new_product_specifications, $product_specifications_string, 'product-specifications' ) );
            $product_specifications_array = array();

            foreach ( $product_specifications as $ps ) {
                $product_specifications_array[] = explode( '`', $ps );
            }

            $product->product_specifications = serialize( $product_specifications_array );

            /***** ADD PRODUCT IMAGES *****/

            // Let's hope it's big!
            $image_url = self::IMAGE_BASE . $item[10];
            
            // Setup images array
            $images = explode( '|', $product->images );
			$last_character = substr( $images[0], -1 );

            if ( ( 0 == count( $images ) || empty( $images[0] ) || '.' == $last_character ) && !empty( $item[10] ) && curl::check_file( $image_url ) ) {
				try {
					$image_name = $this->upload_image( $image_url, $product->slug, $product->id, 'furniture' );
				} catch( InvalidParametersException $e ) {
					fn::info( $product );
					echo $product->slug . ' | ' . $image_url . ' | ' . $new_product;
					exit;
				}

                if ( !is_array( $images ) || !in_array( $image_name, $images ) ) {
                    $this->not_identical[] = 'images';
                    $images[] = $image_name;

                    if ( !empty( $item[11] ) && curl::check_file( $image_url ) ) {
                        $image_url = self::IMAGE_BASE . $item[11];
                        try {
                            $images[] = $this->upload_image( $image_url, $product->slug, $product->id, 'furniture' );
                        } catch( InvalidParametersException $e ) {
                            fn::info( $product );
                            echo $product->slug . ' | ' . $image_url . ' | ' . $new_product;
                            exit;
                        }
                    }

                    if ( !empty( $item[12] ) && curl::check_file( $image_url ) ) {
                        $image_url = self::IMAGE_BASE . $item[12];
                        try {
                            $images[] = $this->upload_image( $image_url, $product->slug, $product->id, 'furniture' );
                        } catch( InvalidParametersException $e ) {
                            fn::info( $product );
                            echo $product->slug . ' | ' . $image_url . ' | ' . $new_product;
                            exit;
                        }
                    }

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
				$this->items[$sku] = NULL;
                continue;
            }

            /***** UPDATE PRODUCT *****/

			$product->save();

            // Add on to lists
            $this->existing_products[$product->sku] = $product;
			$this->items[$sku] = NULL;
		}

        $categories = array_unique( $categories );
        echo implode( '<br>', $categories );
        exit;
    }

    /**
     * Send a report
     */
    protected function send_report() {
        // Report just to CTO
        $user = new User();
        $user->get( User::KERRY ); // Kerry Jones

        $subject = 'Butler Feed - ' . dt::now();

        $new_products = @implode( PHP_EOL, $this->new_products );

        $message = 'New Products: ' . count( $this->new_products ) . PHP_EOL;
        $message .= 'Skipped/Unadjusted Products: ' . count( $this->skipped ) . PHP_EOL;
        $message .= str_repeat( PHP_EOL, 2 );
        $message .= "List Of New Products:" . PHP_EOL . $new_products;
        $message .= str_repeat( PHP_EOL, 2 );

        fn::mail( $user->email, $subject, $message );

        // Send report to everyone else
        if( count( $this->new_products ) > 0 ) {
			$message = "-----New Products-----" . PHP_EOL . $new_products;

            //, david@greysuitretail.com, rafferty@greysuitretail.com, chris@greysuitretail.com
			fn::mail( 'kerry@greysuitretail.com', 'Butler Products - ' . dt::now(), $message );
		}
		
		$this->items = $this->existing_products = $this->codes = $this->new_products = $this->non_existent_groups = $new_products = NULL;
    }

    /**
	 * Get Brand
	 *
	 * @param string $retail_sales_category_code
	 * @return int
	 */
	protected function get_brand( $retail_sales_category_code ) {
		return ( array_key_exists( $retail_sales_category_code, $this->codes ) ) ? $this->codes[$retail_sales_category_code] : '';
	}
}
