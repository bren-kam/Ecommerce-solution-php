<?php
/**
 * Handles SiteOnTime API
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class SiteOnTime extends Base_Class {
	const FTP_URL = 'http://www.cmicdata.com/datafeeds/product-data.php';
	const COMPANY_ID = 'B34FF55A-4FC8-4DF7-9FF3-91839248DC7A';
    const USER_ID = 1478; // SiteOnTime

    /**
     * Hold the brand IDs by name
     * @var array
     */
    private $_brand_ids = NULL;

	/**
	 * Creates new Database instance
	 */
	public function __construct() {
		// Load database library into $this->db (can be omitted if not required)
		parent::__construct();

        // Time how long we've been on this page
		$this->curl = new curl();
		$this->p = new Products();
		$this->file = new Files();
        /*
         * SeriesName & ModelDescription > Title
            MenuHeading > Industry
            Category > Sub Category | Sub Category
            Brand > Brand

            SeriesName & ModelDescription > Description
            KeyFeature1 & KeyFeature2 & KeyFeature3 & KeyFeature4 & KeyFeature5 > Description
            StandardColor > Description
            KeyFeature1 & KeyFeature2 & KeyFeature3 & KeyFeature4 & KeyFeature5 > Product Specs
            SKU > SKU
            LargeImage > Images

         */
	}

    /**
     * Run
     */
    public function run() {
        // We need to up the limit
        set_time_limit(300);
        ini_set('memory_limit', '256M');

        // Get products
        $arguments = http_build_query( array( 'cid' => self::COMPANY_ID ) );

        $products = json_decode( curl::get( self::FTP_URL . '?' . $arguments, 240 ) );

        $categories = array();
        foreach ( $products as $p ) {
            $p = $p->{'stdClass Object'};
            $categories[] = $p->Category . ' > ' . $p->SubCategory;
        }

        // Get existing products
        $existing_products = $this->_get_existing_products();

        // Generate array of our items
        $i = $skipped = 0;

        // Initiate product string
        $products_string = '';

        // Any new products get al ink
        $links = array();

		foreach( $products as $product ) {
            // Get the item
            $product = $product->{'stdClass Object'};

            // Prevent freezes
			echo '                    ';

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
			$i++;

            // Create product description
			$item_description = $product->SeriesName . ' ' . $product->ModelDescription;

            // Add key features
            $item_description .= "\n\n\n" . $product->KeyFeature1;
            $item_description .= "\n" . $product->KeyFeature2;
            $item_description .= "\n" . $product->KeyFeature3;
            $item_description .= "\n" . $product->KeyFeature4;
            $item_description .= "\n" . $product->KeyFeature5;

            // Add Color
            $item_description .= "\n\n\n" . $product->StandardColor;

            // Add Category
            $item_description .= "\n\n\nCategory: " . $product->Category . ' > ' . $product->SubCategory;

            // Define the description as it needs to be
			$description = format::autop( format::unautop( '<p>' . $item_description . '</p>' ) );

			$sku = $product->SKU;

            // This is serialized -- first field is name, second is content (name is optional) third is sequence
			$product_specs = '`' . htmlentities( $product->KeyFeature1, ENT_QUOTES, 'UTF-8' ) . '`0';
			$product_specs .= '|`' . htmlentities( $product->KeyFeature2, ENT_QUOTES, 'UTF-8' ) . '`1';
			$product_specs .= '|`' . htmlentities( $product->KeyFeature3, ENT_QUOTES, 'UTF-8' ) . '`2';
			$product_specs .= '|`' . htmlentities( $product->KeyFeature4, ENT_QUOTES, 'UTF-8' ) . '`3';
			$product_specs .= '|`' . htmlentities( $product->KeyFeature5, ENT_QUOTES, 'UTF-8' ) . '`4';

            // No reporting for weight and volume
			$weight = $volume = $price = $list_price = 0;

            // Get name and slug
			$name = $product->SeriesName . ' ' . $product->ModelDescription;
			$slug = str_replace( '---', '-', format::slug( $name ) );

            // Get the brand ID -- create it if necessary
			$brand_id = $this->_get_brand_id( $product->Brand );
            
            // Let's hope it's big!
			$image = $product->LargeImage;

			$images = array();

			////////////////////////////////////////////////
			// Get/Create the product
			if ( array_key_exists( $sku, $existing_products ) ) {
				$identical = true;

				$product = $existing_products[$sku];
				$product_id = $product['product_id'];

				$product_images = explode( '|', $product['images'] );

				// Override data with existing data
				if( empty( $name ) ) {
					$name = $product['name'];
				} elseif ( $name != $product['name'] ) {
					$identical = false;
				}

				if( empty( $slug ) ) {
					$slug = $product['slug'];
				} elseif ( $slug != $product['slug'] ) {
					$slug = $this->_unique_slug( $slug );

					if ( $slug != $product['slug'] )
						$identical = false;
				}

				if( empty( $description ) ) {
					$description = format::autop( format::unautop( $product['description'] ) );
				} elseif ( $description != format::autop( format::unautop( $product['description'] ) ) ) {
					$identical = false;
				}

				$images = $product_images;

				if ( 0 == count( $images ) && !empty( $image ) && curl::check_file( $image ) ) {
					$identical = false;
					$image_name = $this->upload_image( $image, $slug, $product_id, $industry );

					if ( !is_array( $images ) || !in_array( $image_name, $images ) )
						$images[] = $image_name;
				}

				$product_specifications = '';

				$product['product_specifications'] = unserialize( $product['product_specifications'] );
				if( is_array( $product['product_specifications'] ) )
				foreach( $product['product_specifications'] as $ps ) {
					if( !empty( $product_specifications ) )
						$product_specifications .= '|';

					$product_specifications .= html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ) . '`' . html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ) . '`' . $ps[2];
				}

				if( empty( $product_specs ) ) {
					$product_specs = $product_specifications;
				} elseif ( $product_specs != $product_specifications ) {
					$identical = false;
				}

				if( empty( $brand_id ) ) {
					$brand_id = $product['brand_id'];
				} elseif ( $brand_id != $product['brand_id'] ) {
					$identical = false;
				}

				if( empty( $product_status ) ) {
					$product_status = $product['status'];
					$links['updated-product'][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";
				} else {
					$links[$product_status][] = $name . "\nhttp://admin.greysuitretail.com/products/add-edit/?pid=$product_id\n";

					if ( $product_status != $product['status'] )
						$identical = false;
				}

				$publish_visibility = $product['publish_visibility'];
				$publish_date = $product['publish_date'];

				if( empty( $weight ) ) {
					$weight = $product['weight'];
				} elseif ( $weight != $product['weight'] ) {
					$identical = false;
				}

				if( empty( $volume ) ) {
					$volume = $product['volume'];
				} elseif ( $volume != $product['volume'] ) {
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

                // Make sure it's a unique slug
                $slug = $this->_unique_slug( $slug );

				// Upload image if it's not blank
				if ( !empty( $image ) && curl::check_file( $image ) ) {
                    $image_name = $this->upload_image( $image, $slug, $product_id, $industry );

					if ( !in_array( $image_name, $images ) )
						$images[] = $image_name;
				}

				$price = $list_price = 0;
				$publish_visibility = 'private';
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
                $products[$sku] = compact( 'name', 'slug', 'description', 'product-status', 'sku', 'price', 'list_price', 'product_specs', 'brand_id', 'publish_visibility', 'publish_date', 'product_id', 'weight', 'volume', 'images' );
			}

			// Update the product
			$this->p->update( $name, $slug, $description, 'in-stock', $sku, $price, $list_price, $product_specs, $brand_id, $industry_id, $publish_visibility, $publish_date, $product_id, $weight, $volume );

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
			exit;
		}

        echo "Skipped: $skipped<br />\n";
		echo $i;
		echo '|' . memory_get_peak_usage(true) . '-' . memory_get_usage(true);

		$headers = "From: noreply@greysuitretail.com" . "\r\n" .
			"Reply-to: noreply@greysuitretail.com" . "\r\n" .
			"X-Mailer: PHP/" . phpversion();

		mail( 'kerry@studio98.com', 'Site On Time - ', $products_string, $headers );

		if ( is_array( $links['new-products'] ) ) {
			$message = '';

			$message .= "-----New Products-----\n";
			$message .= implode( "\n", $links['new-products'] );

			mail( 'kerry@greysuitretail.com, david@greysuitretail.com, rafferty@greysuitretail.com, chris@greysuitretail.com', 'Site On Time', $message, $headers );
		}
    }

    /**
	 * Upload image
	 *
	 * @param string $image_url
	 * @param string $slug
	 * @param int $product_id
	 * @param string $industry
     * @return string
	 */
	public function upload_image( $image_url, $slug, $product_id, $industry ) {
		$new_image_name = $slug;
		$image_extension = strtolower( f::extension( $image_url ) );
		
		$image['name'] = "{$new_image_name}.{$image_extension}";
		$image['tmp_name'] = '/gsr/systems/backend/admin/media/downloads/scratchy/' . $image['name'];

		if( is_file( $image['tmp_name'] ) && curl::check_file( 'http://' . $industry . ".retailcatalog.us/products/$product_id/thumbnail/$new_image_name.$image_extension" ) )
			return "$new_image_name.$image_extension";
		
		$fp = fopen( $image['tmp_name'], 'wb' );
		
		$this->curl->save_file( $image_url, $fp );
		
		fclose( $fp );
		
		$this->file->upload_image( $image, $new_image_name, 320, 320, $industry, 'products/' . $product_id . '/' );
		$this->file->upload_image( $image, $new_image_name, 46, 46, $industry, 'products/' . $product_id  . '/thumbnail/' );
		$this->file->upload_image( $image, $new_image_name, 200, 200, $industry, 'products/' . $product_id . '/small/' );
		$new_image_name = $this->file->upload_image( $image, $new_image_name, 700, 700, $industry, 'products/' . $product_id . '/large/' );

		if( file_exists( $image['tmp_name'] ) )
			@unlink( $image['tmp_name'] );

		return $new_image_name;
	}

    /**
     * Get a list of all the brands
     *
     * @param string $name
     * @return array
     */
    private function _get_brand_id( $name ) {
        // Make sure the brands are in place
        $this->_load_brands();

        // Check to make sure we have the brand
        if ( !isset( $this->_brand_ids[$name] ) )
            $this->_brand_ids[$name] = $this->_create_brand( $name );

        return $this->_brand_ids[$name];
    }

    /**
     * Load the brands
     */
    private function _load_brands() {
        if ( is_array( $this->_brand_ids ) )
            return;

        // Load the brands
        $b = new Brands();
        $all_brands = $b->get_all();

        // We just want the names
        foreach ( $all_brands as $brand ) {
            $this->_brand_ids[$brand['name']] = $brand['brand_id'];
        }
    }

    /**
     * Return brand id
     *
     * @param $name
     * @return int
     */
    private function _create_brand( $name ) {
        $b = new Brands();

        // Return the brand id
        return $b->create_simple( $name );
    }

    /**
	 * Get Products
	 *
	 * @return array
	 */
	private function _get_existing_products() {
		$products = $this->db->get_results( "SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`name`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`price`, a.`weight`, a.`volume`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, b.`name` AS industry, GROUP_CONCAT( `image` ORDER BY `sequence` ASC SEPARATOR '|' ) AS images FROM `products` AS a INNER JOIN `industries` AS b ON (a.`industry_id` = b.`industry_id`) LEFT JOIN `product_images` AS c ON ( a.`product_id` = c.`product_id` ) WHERE a.`user_id_created` = " . self::USER_ID . " GROUP BY a.`product_id`", ARRAY_A );

		// Handle any error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to get products.', __LINE__, __METHOD__ );
			return false;
		}

		return ar::assign_key( $products, 'sku' );
	}

    /**
     * Check to see if a Slug is already being used
     *
     * @param string $slug
     * @return string
     */
    private function _unique_slug( $slug ) {
        $existing_slug = $this->db->get_var( "SELECT `slug` FROM `products` WHERE `user_id_created` = " . self::USER_ID ." AND `publish_visibility` <> 'deleted' AND `slug` = '" . $this->db->escape( $slug ) . "'" );

        // Handle any error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to check slug.', __LINE__, __METHOD__ );
			return false;
		}

        // See if the slug already exists
        if ( $slug == $existing_slug ) {
            // Check to see if it has been incremented before
            if ( preg_match( '/-([0-9]+)$/', $slug, $matches ) > 0 ) {
                // The number to increment it by
                $increment = $matches[1] * 1 + 1;

                // Give it the new increment
                $slug = preg_replace( '/-[0-9]+$/', "-$increment", $slug );

                // Make sure it's unique
                $slug = $this->_unique_slug( $slug );
            } else {
                // It has not been incremented before, start with 2
                $slug .= '-2';
            }
        }

        // Return the unique slug
        return $slug;
    }

	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}