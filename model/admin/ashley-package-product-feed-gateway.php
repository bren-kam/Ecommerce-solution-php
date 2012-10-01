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
     * @var Ashley_API
     */
    protected $ashley;

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
        
        // Get librarys
        library('ashley-api/ashley-api');
        $this->ashley = new Ashley_API();
    }

    /**
     * Get Data from Ashley
     */
    protected function get_data() {
        // Get al ist of the files
        $files = $this->ftp->dir_list();

        $count = count( $files );

        while ( is_null( $this->file ) && 0 != $count ) {
            $last_file = array_pop( $files );

            if ( 'xml' == f::extension( $last_file ) )
                $this->file = $last_file;

            $count = count( $files );
        }

        $xml_reader = new XMLReader();

		// Grab the latest file
		if( !file_exists( '/gsr/systems/backend/admin/media/downloads/ashley/' . $this->file ) )
			$this->ftp->get( $this->file, '', '/gsr/systems/backend/admin/media/downloads/ashley/' );

		$xml_reader->open( '/gsr/systems/backend/admin/media/downloads/ashley/' . $this->file );

		$j = -1;

		while( $xml_reader->read() ) {
			switch ( $xml_reader->localName ) {
				case 'item':
					// Make sure we're not dealing with an end element
					if( $xml_reader->nodeType == XMLReader::END_ELEMENT ) {
						$xml_reader->next();
						continue;
					}

					// Increment the item
					$j++;

					// Set the dimensions
					$dimensions = 0;

					// Create base for items
					$this->items[$j] = array(
						'status' => ( 'Discontinued' == trim( $xml_reader->getAttribute('itemStatus') ) ) ? 'discontinued' : 'in-stock'
						, 'nodeType' => trim( $xml_reader->nodeType )
						, 'group' => trim( $xml_reader->getAttribute('itemGroupCode') )
						, 'image' => trim( $xml_reader->getAttribute('image') )
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

            $product->name = $this->identical( $name, $product->name, 'name' );
            $product->slug = $this->identical( str_replace( '---', '-', format::slug( $name ) ), $product->slug, 'slug' );
            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( $item['status'], $product->status, 'status' );
            $product->product_specifications = $this->identical( $item['specs'], $product->product_specifications, 'product-specifications' );
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
            $image_url = 'http://www.studio98.com/ashley/Images/' . $image;

            // Setup images array
            $images = explode( '|', $product->images );

            if ( ( 0 == count( $images ) || empty( $images[0] ) ) && !empty( $image ) && !in_array( $image, array( 'Blank.gif', 'NOIMAGEAVAILABLE_BIG.jpg' ) ) && curl::check_file( $image_url ) ) {
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
		return $this->codes[$retail_sales_category_code];
	}
}
