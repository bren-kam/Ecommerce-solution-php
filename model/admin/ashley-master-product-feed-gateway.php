<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class AshleyMasterProductFeedGateway extends ProductFeedGateway {
    const FTP_URL = 'ftp.ashleyfurniture.com';
	const USERNAME = 'CE_3400-';
	const PASSWORD = 'gRwfUn#';
    const USER_ID = 353; // Ashley

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
     * Hold the category code translation
     */
    protected $codes = array(
			'AB' => 8
			, 'AD' => 8
			, 'AS' => 8
			, 'AT' => 8
			, 'MB' => 171
			, 'MD' => 171
			, 'BF' => 8
			, 'BL' => 8
			, 'BV' => 8
			, 'DB' => 170
			, 'DD' => 170
			, 'DT' => 170
			, 'SB' => 170
			, 'SD' => 170
			, 'DH' => 170
			, 'DM' => 170
			, 'DS' => 170
			, 'DC' => 170
			, 'SS' => 170
			, 'SH' => 170
			, 'SM' => 170
			, 'SC' => 170
			, 'AH' => 8
			, 'AM' => 8
			, 'AO' => 8
			, 'AC' => 8
			, 'MH' => 171
			, 'MM' => 171
			, 'MS' => 171
			, 'MC' => 171
			, 'UA' => 8
			, 'UU' => 8
			, 'UO' => 8
			, 'MO' => 171
			, 'MU' => 171
			, 'DA' => 170
			, 'DO' => 170
			, 'DU' => 170
			, 'SO' => 170
			, 'SU' => 170
			, 'ZZ' => 8
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

				// Weight
				case 'unitPrice':
                    $this->items[$j]['price'] = trim( $xml_reader->getAttribute('price') );
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

            // Ticket 17005 said to no longer change these.
			if ( $new_product || empty( $product->slug ) ) {
				$product->name = $name;
				$product->slug = str_replace( '---', '-', format::slug( $name ) );
			}
            // $product->name = $this->identical( $name, $product->name, 'name' );
            // $product->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $product->slug, 'slug' );

            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( $item['status'], $product->status, 'status' );
            $product->price = $this->identical( $item['price'], $product->price, 'price' );
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
