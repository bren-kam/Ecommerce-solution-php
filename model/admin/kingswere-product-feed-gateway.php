<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class KingswereProductFeedGateway extends ProductFeedGateway {
    const FTP_URL = 'ftp.ashleyfurniture.com';
	const USERNAME = 'CE_3400-';
	const PASSWORD = 'gRwfUn#';
    const USER_ID = 2706; // Kingswere - US
    const BRAND_ID = 825; // Kingswere

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
     * Hold the file to XML data from
     * @var string|null
     */
    protected $file = null;

    /**
     * Hold FTP
     * @var FTP
     */
    protected $ftp;

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
        $file = '888-3800-0-20140129152749-EN-US.xml';
        $xml_reader = new XMLReader();
		$xml_reader->open( '/gsr/systems/backend-testing/' . $file );
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
						, 'brand_id' => self::BRAND_ID
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
						$this->items[$j]['specs'][] = array( 'Depth', trim( $xml_reader->getAttribute('value') ) . ' Inches' ) ;
				break;

				// Specifications
				case 'height':
					if ( isset( $dimensions ) && $dimensions && 'Inches' == trim( $xml_reader->getAttribute('unitOfMeasure') ) )
                        $this->items[$j]['specs'][] = array( 'Height', trim( $xml_reader->getAttribute('value') ) . ' Inches' ) ;
				break;

				// Specifications
				case 'length':
					if ( isset( $dimensions ) && $dimensions && 'Inches' == trim( $xml_reader->getAttribute('unitOfMeasure') ) )
                        $this->items[$j]['specs'][] = array( 'Length', trim( $xml_reader->getAttribute('value') ) . ' Inches' ) ;

					$dimensions = 0;
				break;

				// Weight
				case 'weight':
					if ( !isset( $this->items[$j]['weight'] ) )
						$this->items[$j]['weight'] = trim( $xml_reader->getAttribute('value') );
				break;

				// Weight
				case 'unitPrice':
					if ( !isset( $this->items[$j]['price'] ) )
						$this->items[$j]['price'] = (float) trim( $xml_reader->readString() );
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
		
		$xml_reader = $files = NULL;
    }

    /**
     * Now process everything with the data we have
     */
    protected function process() {
		fn::info( $this->items );exit;
        // Generate array of our items
		foreach( $this->items as $item_key => $item ) {
			/***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            echo "AMF: $item_key\n";
            set_time_limit(30);
			flush();

            // Reset errors
            $this->reset_error();

            /***** CHECK PRODUCT *****/

            // Setup the variables to see if we should continue
			$sku = $item['sku'];

            // We can't have a SKU like B457B532 -- it means it is international and comes in a container
			$this->check( !preg_match( '/[lL]?[0-9-]+[a-zA-Z][0-9-]+/', $sku ) );

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
				$new_product = true;
                $product = new Product();
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'private';
                $product->create();

                // Set publish date
                $product->publish_date = dt::now();

                // Increment product count
                $this->new_product( format::convert_characters( $this->groups[$item['group']]['name'] . ' - ' . $item['description'] ) . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid={$product->id}\n" );
            } else {
                $new_product = false;
				$product->user_id_modified = self::USER_ID;
			}

            $product->get_specifications();

            /***** PREPARE PRODUCT DATA *****/

            $group = $this->groups[$item['group']];
            $group_name = $group['name'] . ' - ';

            $group_description = '<p>' . $group['description'] . '</p>';
            $group_features = '<p>' . $group['features'] . '</p>';

			$name = format::convert_characters( $group_name . $item['description'] );

            /***** ADD PRODUCT DATA *****/

            // Reset the product to being "not" identical
            $this->reset_identical();

            $product->industry_id = 1;

            // Ticket 17005 said to no longer change these.
			if ( $new_product || empty( $product->slug ) ) {
				$product->name = $name;
				$product->slug = str_replace( '---', '-', format::slug( $name ) );
			}

            // Handle categories
			if ( $new_product || empty( $product->category_id ) ) {
                // Get category
                $product->category_id = $this->get_category( $product->sku, $product->name );
			}

            // $product->name = $this->identical( $name, $product->name, 'name' );
            // $product->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $product->slug, 'slug' );

            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( $item['status'], $product->status, 'status' );
            $product->price = $this->identical( $item['price'], $product->price, 'price' );
            $product->weight = $this->identical( $item['weight'], $product->weight, 'weight' );
            $product->brand_id = $this->identical( $item['brand_id'], $product->brand_id, 'brand' );
            $product->description = $this->identical( format::convert_characters( format::autop( format::unautop( '<p>' . $item['description'] . "</p>{$group_description}{$group_features}" ) ) ), format::autop( format::unautop( $product->description ) ), 'description' );

            /***** ADD PRODUCT IMAGES *****/

            // Let's hope it's big!
			$image = $item['image'];
            $image_url = 'https://www.ashleydirect.com/graphics/' . $image;
            
            // Setup images array
            $images = explode( '|', $product->images );
			$last_character = substr( $images[0], -1 );
			
            if ( ( 0 == count( $images ) || empty( $images[0] ) || '.' == $last_character ) && !empty( $image ) && !in_array( $image, array( 'Blank.gif', 'NOIMAGEAVAILABLE_BIG.jpg' ) ) && curl::check_file( $image_url ) ) {
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
				$this->items[$item_key] = NULL;
                continue;
            }

            /***** UPDATE PRODUCT *****/

			$product->save();

            // Add specs
            $product->delete_specifications();
            $product->add_specifications( $item['specs'] );

            // Add on to lists
            $this->existing_products[$product->sku] = $product;
			$this->items[$item_key] = NULL;
		}
    }

    /**
     * Send a report
     */
    protected function send_report() {
        // Report just to CTO
        $user = new User();
        $user->get( User::KERRY ); // Kerry Jones

        $subject = 'Kingswere Feed - ' . dt::now();

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
		
		$this->items = $this->existing_products = $this->codes = $this->new_products = $this->non_existent_groups = $new_products = NULL;
    }

    /**
     * Get Category
     *
     * @param string $sku
     * @param string $name
     * @return int
     */
    protected function get_category( $sku, $name ) {
        // Setup
        $category_id = 0;
        $length = strlen( $sku );
        $first_character = $sku[0];
        $last_character = substr( $sku, -1 );

        if ( 7 == $length && is_numeric( $first_character ) ) {
            // Living Room & Leather
            $relevant_sku = substr( $sku, 5, 2 );

            // Living Room
            switch ( $relevant_sku ) {
                case 60:
                case 23:
                case 21:
                case 20:
                case 46: // Chairs
                    $category_id = 221;
                break;

                case 76:
                case 70:
                case 69:
                case 04:
                case 01:
                case 49:
                case 48:
                case 77:
                case 17:
                case 16:
                case 34:
                case 67:
                case 56:
                case 66:
                case 55: // Sectional Pieces
                    $category_id = 695;
                break;

                case 14:
                case 13:
                case '08': // Ottomans
                    $category_id = 229;
                break;

                case 35: // Loveseat
                    $category_id = 220;
                break;

                case 73: // Chair w/ Ottoman
                    $category_id = 692;
                break;

                case 37:
                case 36:
                case 39: // Sleeper Sofa
                    $category_id = 425;
                break;

                case 38: // Sofa
                    $category_id = 219;
                break;

                case 15:
                case 18: // Chaise
                    $category_id = 249;
                break;

                case 31:
                case 29:
                case 61:
                case 26:
                case 30:
                case 25: // Recliners
                    $category_id = 222;
                break;

                case 86:
                case 94:
                case 43: // Reclining Furniture > Reclining Love Seats
                    $category_id = 227;
                break;

                case 74:
                case 96:
                case 91: // Reclining Power Loveseat
                    $category_id = 671;
                break;

                case 98: // Power Recliner
                    $category_id = 672;
                break;

                case 47:
                case 87: // Reclining Power Sofa
                    $category_id = 670;
                break;

                case 81:
                case 88: // Reclining Furniture > Reclining Sofas
                    $category_id = 224;
                break;
            }
        } elseif ( 'T' == $first_character ) {
            // Living Room > Occasional
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case 407:
                case 202:
                case 102:
                case 3:
                case 7:
                case 699:
                case 684:
                case 668:
                case 477:
                case 371: // Chair Side Tables
                    $category_id = 237;
                break;

                case 13: // Three Pack Table Sets
                    $category_id = 251;
                break;

                case '8T':
                case '8B':
                case 9;
                case 20:
                case 8:
                case 1:
                case 0: // Cocktail Table
                    $category_id = 231;
                break;

                case 232:
                case 632:
                case 442:
                case 430:
                case 360:
                case 142:
                case 40: // Accent Cabinet
                    $category_id = 736;
                break;

                case 12:
                case 306:
                case 106: // Accent Table
                    $category_id = 1165;
                break;

                case 615: // Magazine Racks
                    $category_id = 738;
                break;

                case 4: // Sofa Tables
                    $category_id = 234;
                break;

                case 17:
                case 6:
                case 2: // End Tables
                    $category_id = 233;
                break;

                case 705:
                case 804:
                case 504: // Console
                    $category_id = 235;
                break;

                case 11: // Cabinet
                    $category_id = 657;
                break;
            }
        } elseif ( 'W' == $first_character ) {
            // Home Entertainment
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case '01': // Entertainment Accessories
                    $category_id = 1166;
                break;

                case 78:
                case 22:
                case 68:
                case 50:
                case 12:
                case '23H':
                case '21H':
                case 11:
                case 20:
                case 80:
                case 58:
                case '60H':
                case 48:
                case 22:
                case 31:
                case 21:
                case 38:
                case 28:
                case 18:
                case 17:
                case 10:
                case 60: // Consoles
                    $category_id = 333;
                break;

                case 27:
                case 35:
                case 33:
                case 25:
                case 24:
                case 34:
                case '23B':
                case 23:
                case 26: // Wall Systems
                    $category_id = 336;
                break;

                case 36: // Media Storage Cabinets
                    $category_id = 622;
                break;

                case 400:
                case 40: // Corner Cabinets
                    $category_id = 334;
                break;
            }
        } elseif ( 'D' == $first_character ) {
            // Dining Room
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case '13T':
                case '13B':
                case '15T':
                case '15B':
                case 32:
                case 325:
                case 125:
                case '50T':
                case '50B':
                case 13:
                case 21:
                case 26:
                case 25:
                case 15: // Tables
                    $category_id = 130;
                break;

                case 223:
                case 65: // Pub Tables
                    $category_id = 144;
                break;

                case 225: // Dining Room Groups
                    $category_id = 347;
                break;

                case 524:
                case 424:
                case 224:
                case 323:
                case 320:
                case 324:
                case 130:
                case 124:
                case 230: // Bar Stools
                    $category_id = 142;
                break;

                case 76:
                case 360:
                case 160:
                case 59:
                case 60: // Servers
                    $category_id = 726;
                break;

                case '05':
                case 102:
                case 202:
                case 101:
                case '06':
                case '09':
                case '07':
                case '04':
                case '03':
                case '01': // Side Chairs
                    $category_id = 132;
                break;

                case 80: // Buffets
                    $category_id = 133;
                break;

                case '00': // Benches
                    $category_id = 141;
                break;

                case '03A':
                case '02A':
                case '01A': // Arm Chairs
                    $category_id = 131;
                break;

                case '65T':
                case '65B':
                case 65: // Side Boards
                    $category_id = 134;
                break;

                case 76: // Bakers Racks
                    $category_id = 135;
                break;

                case 61:
                case 81: // China Cabinets
                    $category_id = 212;
                break;
            }
        } elseif ( 'B' == $first_character ) {
            // Bedroom / Kid's Furniture
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                /***** Kid's Furniture *****/

                case '01': // Chairs
                    $category_id = 279;
                break;

                case '13R':
                case '13L';
                case '68T':
                case '68B': // Beds > Loft
                    $category_id = 688;
                break;

                case '59B': // Beds > Beds
                    $category_id = 273;
                break;

                case 83:
                case 82:
                case 86:
                case '59S':
                case '59R':
                case '59P':
                case '50T':
                case '50D':
                case '20R': // Beds > Bed Frame
                    $category_id = 700;
                break;

                case 87:
                case 53:
                case 52: // Beds > Headboard
                    $category_id = 698;
                break;

                case 84:
                case 51: // Beds > Footboard
                    $category_id = 699;
                break;

                case '20L';
                case 22:
                case 52: // Desks
                    $category_id = 277;
                break;

                case 20;
                case 17:
                case 16: // Bookcase
                    $category_id = 417;
                break;

                case 19:
                case 18: // Chests
                    $category_id = 272;
                break;

                case 21: // Dressers
                    $category_id = 270;
                break;

                case 23: // Hutch
                    $category_id = 278;
                break;

                case 26: // Mirrors
                    $category_id = 269;
                break;

                case 38: // Media Chests
                    $category_id = 624;
                break;

                case '68B':
                case 60:
                case 59:
                case '58S':
                case '57S':
                case '57P':
                case '058':
                case '008': // Bunk Beds
                    $category_id = 617;
                break;

                case 80: // Daybed
                    $category_id = 282;
                break;

                /***** Bedroom *****/

                case '09':
                case '00': // Benches
                    $category_id = 569;
                break;

                case 46:
                case 35:
                case 28: // Mirrors
                    $category_id = 102;
                break;

                case 31: // Dressers
                    $category_id = 101;
                break;

                case 39:
                case 38:
                case 41:
                case 40: // Media Chests
                    $category_id = 107;
                break;

                case 47:
                case 46:
                case 45:
                case 43: // Chests
                    $category_id = 103;
                break;

                case '50R':
                case '50L': // Storage Cabinet
                    $category_id = 626;
                break;

                case '91R':
                case '91L':
                case 193:
                case 91:
                case 92:
                case 93: // Nightstand
                    $category_id = 105;
                break;

                case '49T':
                case '49B': // Armoire
                    $category_id = 104;
                break;

                case 68:
                case 58:
                case 57:
                case 82:
                case 81:
                case 55:
                case 67:
                case 394:
                case 357:
                case 357:
                case 258:
                case 257:
                case 150:
                case 158:
                case 157:
                case 78:
                case 77:
                case '71N': // Headboards
                    $category_id = 125;
                break;

                case '166S':
                case 166:
                case '164S':
                case 164:
                case 151:
                case 356:
                case 354:
                case 294:
                case 256:
                case 254:
                case 194:
                case 154:
                case 156:
                case '66N':
                case '64N':
                case '64S':
                case 76:
                case 74:
                case 64:
                case 54:
                case 50:
                case '66S':
                case 66:
                case 56:
                case 70: // Footboards
                    $category_id = 428;
                break;

                case '97S':
                case '96S':
                case 95:
                case '94S':
                case 94:
                case 256:
                case 254:
                case 199:
                case 197:
                case 196:
                case 195:
                case 194:
                case 97:
                case '99N':
                case '98N':
                case 96:
                case 98:
                case 99: // Bed Frames
                    $category_id = 126;
                break;
            }
        } elseif ( 'H' == $first_character ) {
            // Home Furniture
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case '19H':
                case 49:
                case 48: // Hutch
                    $category_id = 583;
                break;

                case '23H':
                case 47:
                case 23:
                case 24:
                case 45:
                case '27R':
                case 46:
                case 29:
                case 27:
                case 44:
                case 26:
                case 10:
                case 19: // Home Office Desks
                    $category_id = 329;
                break;

                case 25:
                case 40:
                case 42:
                case 12: // Home Office File Cabinets and Carts
                    $category_id = 330;
                break;

                case '01A': // Home Office Desk Chair
                    $category_id = 437;
                break;

                case 47:
                case 34:
                case '70T':
                case '70B':
                case 18:
                case 17:
                case 16:
                case 15: // Bookcases
                    $category_id = 452;
                break;
            }
        } elseif ( 'M' == $first_character ) {
            // Mattresses

            if ( stristr( $name, 'Pillowtop' ) ) {
                // Pillowtop mattresses
                $category_id = 167;
            } elseif ( stristr( $name, 'Plush' ) ) {
                // Plush Mattresses
                $category_id = 166;
            } elseif ( stristr( $name, 'Latex' ) ) {
                // Latex Mattresses
                $category_id = 169;
            } elseif ( stristr( $name, 'Gel' ) ) {
                // Gel mattresses
                $category_id = 564;
            } else {
                // Memory Foam Mattresses
                $category_id = 168;
            }
        } elseif ( 'Q' == $first_character && 8 == $length && !is_numeric( $last_character ) ) {
            // Bedding > Bedding Ensembles
            $category_id = 179;
        }  elseif ( 'L' == $first_character && 7 == $length ) {
            // Accessories > Lamps
            $category_id = 194;
        } elseif ( 'R' == $first_character && 7 == $length ) {
            // Accessories > Rugs
            $category_id = 338;
        } elseif ( 'AC2' == substr( $sku, 0, 3 ) && 8 == $length ) {
            // Accessories > Table Tops
            $category_id = 341;
        } elseif ( 'M89' == substr( $sku, 0, 3 ) && 6 == $length ) {
            // Bedding > Pillows
            $category_id = 597;
        } elseif ( stristr( $name, 'Throw' ) ) {
            // Accessories > Throws
            $category_id = 342;
        } elseif ( stristr( $name, 'Wall Art' ) ) {
            // Accessories > Wall Art
            $category_id = 339;
        } elseif ( 'A' == $first_character && stristr( $name, 'Pillow' ) ) {
            // Bedding > Pillows
            $category_id = 597;
        }

        return $category_id;
    }
}
