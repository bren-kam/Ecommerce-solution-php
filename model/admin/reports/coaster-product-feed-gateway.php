<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class CoasterProductFeedGateway extends ProductFeedGateway {
    const USER_ID = 1915; // Coaster

    /**
     * Hold the file to XML data from
     * @var string|null
     */
    protected $file = null;

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

        // Time how long we've been on this page
        $this->ftp = new Ftp( '/CustEDI/3400-/Outbound/' );

        // Set login information
		$this->ftp->host     = self::FTP_URL;
		$this->ftp->username = self::USERNAME;
		$this->ftp->password = self::PASSWORD;
		$this->ftp->port     = 21;

		// Connect
		$this->ftp->connect();
    }

    /**
     * Get Data from Ashley
     */
    protected function get_data() {
        // Get al ist of the files
        $files = $this->ftp->dir_list();

        $count = count( $files );
		
        while ( !isset( $file ) && 0 != $count ) {
            $last_file = array_pop( $files );

            if ( 'xml' == f::extension( $last_file ) )
                $file = $last_file;
			
            $count = count( $files );
        }

        $xml_reader = new XMLReader();

		// Grab the latest file
		if( !file_exists( '/gsr/systems/backend/admin/media/downloads/ashley/' . $file ) )
			$this->ftp->get( $file, '', '/gsr/systems/backend/admin/media/downloads/ashley/' );
		
		$xml_reader->open( '/gsr/systems/backend/admin/media/downloads/ashley/' . $file );
		$j = -1;

		while( $xml_reader->read() ) {
			switch ( $xml_reader->localName ) {
				case 'item':
					// Make sure we're not dealing with an end element
					if( $xml_reader->nodeType == XMLReader::END_ELEMENT ) {
						$xml_reader->next();
						continue;
					}
					
					$image = trim( $xml_reader->getAttribute('image') );
					
					if ( empty( $image ) )
						continue;

					// Increment the item
					$j++;

					// Set the dimensions
					$dimensions = 0;
					

					// Create base for items
					$this->items[$j] = array(
						'status' => ( 'Discontinued' == trim( $xml_reader->getAttribute('itemStatus') ) ) ? 'discontinued' : 'in-stock'
						, 'nodeType' => trim( $xml_reader->nodeType )
						, 'group' => trim( $xml_reader->getAttribute('itemGroupCode') )
						, 'image' => $image
						, 'brand_id' => $this->get_brand( trim( $xml_reader->getAttribute('retailSalesCategory') ) )
						, 'specs' => ''
						, 'weight' => 0
						, 'volume' => 0
					);

				break;

				// SKU
				case 'itemIdentifier':
					if ( !isset( $this->items[$j]['sku'] ) )
						$this->items[$j]['sku'] = trim( $xml_reader->getAttribute('itemNumber') );
				break;

				// Description
				case 'itemDescription':
					$this->items[$j]['description'] = trim( $xml_reader->getAttribute('itemFriendlyDescription') );
				break;

				// We're in the item dimensions section
				case 'itemDimensions':
					$dimensions = 1;
				break;

                // Turn off so it doesn't get overridden by package characteristics
                case 'packageDimensions':
                    $dimensions = 0;
                break;

				// Specifications
				case 'depth':
					if ( isset( $dimensions ) && $dimensions && 'Inches' == trim( $xml_reader->getAttribute('unitOfMeasure') ) )
						$this->items[$j]['specs'] = 'Depth`' . trim( $xml_reader->getAttribute('value') );
				break;

				// Specifications
				case 'height':
					if ( isset( $dimensions ) && $dimensions && 'Inches' == trim( $xml_reader->getAttribute('unitOfMeasure') ) )
						$this->items[$j]['specs'] .= ' Inches`0|Height`' . trim( $xml_reader->getAttribute('value') );
				break;

				// Specifications
				case 'length':
					if ( isset( $dimensions ) && $dimensions && 'Inches' == trim( $xml_reader->getAttribute('unitOfMeasure') ) )
						$this->items[$j]['specs'] .= ' Inches`1|Length`' . trim( $xml_reader->getAttribute('value') ) . ' Inches`2';

					$dimensions = 0;
				break;

				// Weight
				case 'weight':
					if ( !isset( $this->items[$j]['weight'] ) )
						$this->items[$j]['weight'] = trim( $xml_reader->getAttribute('value') );
				break;

				/*// Volume
				case 'volume':
					if ( !isset( $this->items[$j]['volume'] ) )
						$this->items[$j]['volume'] = trim( $xml_reader->getAttribute('value') );
				break;*/

				// Groups
				case 'groupInformation':
					$this->groups[$xml_reader->getAttribute('groupID')] = array(
						'name' => trim( $xml_reader->getAttribute('groupName') )
						, 'description' => trim( $xml_reader->getAttribute('groupDescription') )
						, 'features' => trim( $xml_reader->getAttribute('groupFeatures') )
					);
				break;
			}
		}

		$xml_reader->close();
    }

    /**
     * Now process everything with the data we have
     */
    protected function process() {
        // Generate array of our items
		foreach( $this->items as $item ) {
			/***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            echo str_repeat( ' ', 50 );
            set_time_limit(30);
			flush();

            // Reset errors
            $this->reset_error();

            /***** CHECK PRODUCT *****/

            // Setup the variables to see if we should continue
			$sku = $item['sku'];

            // We can't have a SKU like B457B532 -- it means it is international and comes in a container
			$this->check( !preg_match( '/[a-zA-Z]?[0-9-]+[a-zA-Z][0-9-]+/', $sku ) );

            if ( !isset( $this->groups[$item['group'] ] ) ) {
                $item['group'] = preg_replace( '/([^-]+)-.*/', '$1', $item['group'] );

                if ( !$this->check( isset( $this->groups[$item['group'] ] ) ) )
                    $this->non_existent_groups[] = $item['group'];
            }

            // If it has an error, don't continue
            if ( $this->has_error() )
                continue;


            /***** GET PRODUCT *****/

            // Get Product
			$product = $this->get_existing_product( $sku );

            // Now we have the product
            if ( !$product instanceof Product ) {
                $product = new Product();
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'private';
                $product->create();

                // Set publish date
                $product->publish_date = dt::now();

                // Increment product count
                $this->new_product( format::convert_characters( $this->groups[$item['group']]['name'] . ' - ' . $item['description'] ) . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid={$product->id}\n" );
            }

            /***** PREPARE PRODUCT DATA *****/

            $group = $this->groups[$item['group']];
            $group_name = $group['name'] . ' - ';

            $group_description = '<p>' . $group['description'] . '</p>';
            $group_features = '<p>' . $group['features'] . '</p>';

			$name = format::convert_characters( $group_name . $item['description'] );

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

            $product->industry_id = 1;
            $product->name = $this->identical( $name, $product->name, 'name' );
            $product->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $product->slug, 'slug' );
            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( $item['status'], $product->status, 'status' );
            $product->weight = $this->identical( $item['weight'], $product->weight, 'weight' );
            $product->brand_id = $this->identical( $item['brand_id'], $product->brand_id, 'brand' );
            $product->description = $this->identical( format::convert_characters( format::autop( format::unautop( '<p>' . $item['description'] . "</p>{$group_description}{$group_features}" ) ) ), format::autop( format::unautop( $product->description ) ), 'description' );

            /** Product Specs are special */
            $product_specifications = explode( '|', $this->identical( $item['specs'], $new_product_specifications, 'product-specifications' ) );

            $product_specifications_array = array();

            foreach ( $product_specifications as $ps ) {
                $product_specifications_array[] = explode( '`', $ps );
            }

            $product->product_specifications = serialize( $product_specifications_array );

            /***** ADD PRODUCT IMAGES *****/

            // Let's hope it's big!
			$image = $item['image'];
            $image_url = 'https://www.ashleydirect.com/graphics/' . $image;
            
            // Setup images array
            $images = explode( '|', $product->images );

            if ( ( 0 == count( $images ) || empty( $images[0] ) ) && !empty( $image ) && !in_array( $image, array( 'Blank.gif', 'NOIMAGEAVAILABLE_BIG.jpg' ) ) && curl::check_file( $image_url ) ) {
                $image_name = $this->upload_image( $image_url, $product->slug, $product->id, 'furniture' );

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

        $subject = 'Ashley Feed - ' . dt::now();

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
	 * Get Brand
	 *
	 * @param string $retail_sales_category_code
	 * @return int
	 */
	protected function get_brand( $retail_sales_category_code ) {
		return ( array_key_exists( $retail_sales_category_code, $this->codes ) ) ? $this->codes[$retail_sales_category_code] : '';
	}
}
