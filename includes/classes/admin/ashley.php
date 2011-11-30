<?php
/**
 * Handles ashley import
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Ashley extends Base_Class {
	const FTP_URL = 'ftp.ashleyfurniture.com';
	const USERNAME = 'CE_3400-';
	const PASSWORD = 'gRwfUn#';
	
	/**
	 * Creates new Database instance
	 *
	 * @return  void
	 */
	public function __construct() {
		// Load database library into $this->db (can be omitted if not required)
		parent::__construct();
		
		// Time how long we've been on this page
		$this->timer_start();
		$this->curl = new curl();
		$this->p = new Products();
		$this->file = new Files();
		
	}

	/**
	 * Main function, goes to page and grabs everything needed and does required actions.
	 * 
	 * @param string $file (optional|)
	 * @return bool
	 */
	public function run( $file = '' ) {
		$ftp = new FTP( 0, '/CustEDI/3400-/Outbound/', true );
		
		ini_set( 'max_execution_time', 600 ); // 10 minutes
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 600 );
		$start = time();
		
		
		// Set login information
		$ftp->host     = self::FTP_URL;
		$ftp->username = self::USERNAME;
		$ftp->password = self::PASSWORD;
		$ftp->port     = 21;
		
		// Connect
		$ftp->connect();
		
		if( empty( $file ) ) {
			// Get al ist of the files
			$files = $ftp->dir_list();
			
			$file = $files[count($files)-1];
		}
		
		
		// Grab the latest file
		if( file_exists( '/home/imaginer/public_html/admin/media/downloads/ashley/' . $file ) ) {
			$this->xml = simplexml_load_file( '/home/imaginer/public_html/admin/media/downloads/ashley/' . $file );
		} else {
			$this->xml = simplexml_load_string( $ftp->ftp_get_contents( $file ) );
		}
		
		//if ( count( $this->xml->items->item ) > 1000 ) {
		//	mail( 'kerry@studio98.com, rafferty@studio98.com', 'Ashley Feed', 'Full List -- skipped' );
		//	return;
		//}
		
		// Initalize variables
		$groups = array();
		$items = array();
		$links = $products_string = '';
		
		// Generate group information
		foreach( $this->xml->groups->groupInformation as $group ) {
			$attributes = $group->attributes();
			
			$groups[trim( $attributes->groupID )] = array( 'name' => trim( $attributes->groupName ), 'description' => trim( $attributes->groupDescription ), 'features' => trim( $attributes->groupFeatures ) );
		}
		
		$i = 0;
		
		// Generate array of our items
		foreach( $this->xml->items->item as $item ) {
			$attributes = $item->attributes();
			$item_description = $item->itemIdentification->itemDescription->attributes();
			
			// Start getting necessary fields G
			$sku = trim( $item->itemIdentification->itemIdentifier[0]->attributes()->itemNumber );
			$product_status = ( 'Discontinued' == $attributes->itemStatus ) ? 'discontinued' : 'in-stock';
			
			if( isset( $item->itemIdentification->itemCharacteristics[0]->itemDimensions ) ) {
				$dimensions = $item->itemIdentification->itemCharacteristics[0]->itemDimensions;
				$product_specs = 'Depth`' . $dimensions->depth->attributes()->value . '`0|Height`' . $dimensions->height->attributes()->value . '`1|Length`' . $dimensions->length->attributes()->value . '`2';
			} else {
				$product_specs = '';
			}
			
			if( isset( $item->itemIdentification->packageCharacteristics->packageDimensions ) ) {
				$package_dimensions = $item->itemIdentification->packageCharacteristics->packageDimensions;
				
				$weight = ( isset( $package_dimensions->weight ) ) ? trim( $package_dimensions->weight->attributes()->value ) : 0;
				$volume = ( isset( $package_dimensions->volume ) ) ? trim( $package_dimensions->volume->attributes()->value ) : 0;
			} else {
				$weight = $volume = 0;
			}
			
			if( isset( $groups[trim( $attributes->itemGroupCode )] ) ) {
				$group = $groups[trim( $attributes->itemGroupCode )];
			
				$group_name = $group['name'] . ' - ';
				$group_description = '<p>' . $group['description'] . '</p>';
				$group_features = '<p>' . $group['features'] . '</p>';
			} else {
				$group_name = $group_description = $group_features = '';
			}
			
			$item_friendly_description = trim( $item_description->itemFriendlyDescription );
			
			$name = $group_name . $item_friendly_description;
			$slug = str_replace( '---', '-', format::slug( $group_name . $item_friendly_description ) );
			$description = format::autop( format::unautop( "<p>$item_friendly_description</p>{$group_description}{$group_features}" ) );
			
			$brand_id = $this->get_brand( trim( $attributes->retailSalesCategory ) );
			
			$image = trim( $attributes->image );
			$images = array();
			
			// Get/Create the product
			if( $product_id = $this->get_product_id( $sku ) ) {
				$product_information = $this->p->get( $product_id );
				$product_images = $this->p->get_images( $product_id );
				$product_images = $product_images[''];
				
				// Override data with existing data
				if( empty( $name ) )
					$name = $product_information['name'];
				
				if( empty( $slug ) )
					$slug = $product_information['slug'];
				
				if( empty( $description ) )
					$description = text::auto_p( format::unautop( $product_information['description'] ) );
				
				$images = $product_images;
				
				if ( 'Blank.gif' != $image && 'NOIMAGEAVAILABLE_BIG.jpg' != $image && curl::check_file( 'http://www.studio98.com/ashley/Images/' . $image ) ) {
					$image_name = $this->upload_image( 'http://www.studio98.com/ashley/Images/' . $image, $slug, $product_id );
					
					if ( !is_array( $images ) || !in_array( $image_name, $images ) )
						$images[] = $image_name;
				}
				
				$price = 0;//$product_information['price'];
				$list_price = 0;//$product_information['list_price'];
				
				if( is_array( $product_information['product_specifications'] ) )
				foreach( $product_information['product_specifications'] as $ps ) {
					if( !empty( $product_specs ) )
						$product_specs .= '|';
					
					$product_specs .= html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ) . '`' . html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ) . '`' . $ps[2];
				}
				
				if( empty( $brand_id ) )
					$brand_id = $product_information['brand_id'];
				
				if( empty( $product_status ) ) {
					$product_status = $product_information['product_status'];
					$links['updated-product'][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";
				} else {
					$links[$product_status][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";
				}
				
				$publish_visibility = $product_information['publish_visibility'];
				$publish_date = $product_information['publish_date'];
				
				if( empty( $weight ) )
					$weight = $product_information['weight'];

				if( empty( $volume ) )
					$volume = $product_information['volume'];
			} else {
				//echo 'wtf';exit;
				$product_id = $this->p->create( 353 );
				
				// Upload image if it's not blank
				if ( 'Blank.gif' != $image && 'NOIMAGEAVAILABLE_BIG.jpg' != $image && curl::check_file( 'http://www.studio98.com/ashley/Images/' . $image ) ) {
					$image_name = $this->upload_image( 'http://www.studio98.com/ashley/Images/' . $image, $slug, $product_id );
					
					if ( !in_array( $image_name, $images ) )
						$images[] = $image_name;
				}
				
				$price = $list_price = 0;
				$publish_visibility = 'private';
				$publish_date = date_time::date( 'Y-m-d' );
				
				$links['new-products'][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";
			}
			
			// Update the product
			$this->p->update( $name, $slug, $description, $product_status, $sku, $price, $list_price, $product_specs, $brand_id, 1, $publish_visibility, $publish_date, $product_id, $weight, $volume );
			
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
			
			$products_string .= $name . "\n";
			
			// We don't want to carry them around in the next loop
			unset( $images );
			
			
			//if ( 5 == $i )
			//	exit;
			//$i++;
			
		}
		
		$headers = "From: noreply@greysuitretail.com" . "\r\n" .
			"Reply-to: noreply@greysuitretail.com" . "\r\n" . 
			"X-Mailer: PHP/" . phpversion();
		
		mail( 'kerry@studio98.com', 'Ashley Feed - ' . $file, $products_string, $headers );
		
		if( is_array( $links ) ) {
			$message = '';
			
			foreach ( $links as $section => $link_array ) {
				$message .= '-----' . ucwords( str_replace( '-', ' ', $section ) ) . "-----\n";
				$message .= implode( "\n", $link_array );
				$message .= "\n\n\n";
			}
			
			mail( 'david@greysuitretail.com, rafferty@greysuitretail.com, chris@greysuitretail.com', 'Ashley Products - ' . $file, $message, $headers );
		}
	}
	
	/**
	 * Get Brand
	 *
	 * @param string $retail_sales_category_code
	 * @return int
	 */
	private function get_brand( $retail_sales_category_code ) {
		$codes = array(
			'AB' => 8,
			'AD' => 8,
			'AS' => 8,
			'AT' => 8,
			'MB' => 171,
			'MD' => 171,
			'BF' => 8,
			'BL' => 8,
			'BV' => 8,
			'DB' => 170,
			'DD' => 170,
			'DT' => 170,
			'SB' => 170,
			'SD' => 170,
			'DH' => 170,
			'DM' => 170,
			'DS' => 170,
			'DC' => 170,
			'SS' => 170,
			'SH' => 170,
			'SM' => 170,
			'SC' => 170,
			'AH' => 8,
			'AM' => 8,
			'AO' => 8,
			'AC' => 8,
			'MH' => 171,
			'MM' => 171,
			'MS' => 171,
			'MC' => 171,
			'UA' => 8,
			'UU' => 8,
			'UO' => 8,
			'MO' => 171,
			'MU' => 171,
			'DA' => 170,
			'DO' => 170,
			'DU' => 170,
			'SO' => 170,
			'SU' => 170,
			'ZZ' => 8
		);
		
		return $codes[$retail_sales_category_code];
	}
	
	/**
	 * Returns product_id
	 *
	 * @param string $sku
	 * @return bool
	 */
	private function get_product_id( $sku ) {
		// Get the product ID
		$product_id = $this->db->get_var( "SELECT `product_id` FROM `products` WHERE `sku` = '" . $this->db->escape( $sku ) . "' AND `publish_visibility` <> 'deleted' AND `user_id_created` = 353" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get product id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_id;
	}
	
	/**
	 * Upload image
	 *
	 * @param string $image_url
	 * @param string $slug
	 * @param int $product_id
	 */
	public function upload_image( $image_url, $slug, $product_id ) {
		$new_image_name = $slug;
		$image_extension = strtolower( format::file_extension( $image_url ) );
		
		$image['name'] = "{$new_image_name}.{$image_extension}";
		$image['tmp_name'] = '/home/imaginer/public_html/admin/media/downloads/scratchy/' . $image['name'];
		
		if( is_file( $image['tmp_name'] ) && curl::check_file( "http://furniture.retailcatalog.us/products/$product_id/thumbnail/$new_image_name.$image_extension" ) )
			return "$new_image_name.$image_extension";
		
		$fp = fopen( $image['tmp_name'], 'wb' );
		
		$this->curl->save_file( $image_url, &$fp );
		
		fclose( $fp );
		
		$this->file->upload_image( $image, $new_image_name, 320, 320, 'furniture', 'products/' . $product_id . '/' );
		$this->file->upload_image( $image, $new_image_name, 46, 46, 'furniture', 'products/' . $product_id  . '/thumbnail/' );
		$this->file->upload_image( $image, $new_image_name, 500, 500, 'furniture', 'products/' . $product_id . '/large/' );
		
		if( file_exists( $image['tmp_name'] ) )
			@unlink( $image['tmp_name'] );
		
		return "$new_image_name.$image_extension";
	}
	
	/**
	 * Gives a report of all the information
	 *
	 * @return array|bool
	 */
	public function report() {
		// Get the page count
		$page_count = $this->db->query( "SELECT DISTINCT `url` FROM `scratchy_pages` WHERE `domain_id` = 3" )->count(); 
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Ashley Bot: Failed to count added pages.', __LINE__, __METHOD__ );
			return false;
		}
		
		$page_links = $this->db->query( "SELECT COUNT(`domain_id`) AS page_link_count FROM `scratchy_page_links` WHERE `domain_id` = 3" )->result( FALSE )->current(); 
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Ashley Bot: Failed to count page links.', __LINE__, __METHOD__ );
			return false;
		}
		
		return array( $page_count, $page_links['page_link_count'] );
	}

	/**
	 * Checks to see if a product with this sku already exists
	 *
	 * @param string $sku
	 * @return bool
	 */
	private function check_sku( $sku ) {
		// Check if it exists 
		$result = $this->db->query( sprintf( "SELECT `product_id` FROM `products` WHERE `sku` = '%s' AND `publish_visibility` <> 'deleted'", format::sql_string( $sku ) ) )->current();
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( "Ashley Bot: Failed to check sku: $sku", __LINE__, __METHOD__ );
			return false;
		}
		
		return !$result;
	}
	
	/**
	 * Logs in
	 *
	 * @since 1.0.0
	 *
	 * @return true
	 */
	private function login() {
		$this->curl->post( $this->login_url, $this->login_post_fields );
		return true;
	}
	
	/**
	 * Starts the timer, for debugging purposes.
	 *
	 * @since 1.0.0
	 */
	private function timer_start() {
		$this->time_start = microtime( true );
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @since 1.0.0
	 *
	 * @return int Total time spent on the query, in seconds
	 */
	private function scratchy_time() {
		return microtime( true ) - $this->time_start;
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}