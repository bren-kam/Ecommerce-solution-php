<?php
/**
 * Handles all the Products
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Products extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Creates a product
	 *
	 * @param int $user_id
	 * @return int
	 */
	public function create( $user_id ) {
		$this->db->insert( 'products', array( 'user_id_created' => $user_id, 'date_created' => dt::date('Y-m-d H:i:s') ), 'is' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Gets a single product
	 *
	 * @param int $product_id the id of the product
	 * @return array
	 */
	public function get( $product_id ) {
		$product = $this->db->get_row( 'SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`name`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`price`, a.`weight`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, b.`name` AS industry  FROM `products` AS a INNER JOIN `industries` AS b ON (a.`industry_id` = b.`industry_id`) WHERE a.`product_id` = ' . (int) $product_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product;
	}
	
	/**
	 * Get images for single product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_images( $product_id ) {
		// Typecast
		$product_id = (int) $product_id;
		
		$product_images = $this->db->get_results( "SELECT `swatch`, `image` FROM `product_images` WHERE `product_id` = $product_id AND `image` <> '' ORDER BY `sequence`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product images.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Define images
		$images = array();
		
		if ( is_array( $product_images ) )
		foreach ( $product_images as $image ) {
			$images[$image['swatch']][] = $image['image'];
		}
		
		return $images;
	}
	
	/**
	 * Get categories
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_categories( $product_id ) {
		$categories = $this->db->get_results( 'SELECT a.`category_id`, b.`name` FROM `product_categories` AS a LEFT JOIN `categories` AS b ON (a.`category_id` = b.`category_id`) WHERE a.`product_id` = ' . (int) $product_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $categories;
	}

	/**
	 * Creates a product and puts it into the database
	 *
	 * @since 1.0.0
	 *
	 * @param string $name 
	 * @param string $slug the product slug
	 * @param string $description full description of the product
	 * @param string $status the status of the product
	 * @param string $sku the product SKU
	 * @param float $price the price
	 * @param float $list_price the list price
	 * @param string $product_specifications the product specifications
	 * @param int $brand_id the brand of the product
	 * @param int $industry_id
	 * @param string $publish_visibility whether the publish should be visibiliy
	 * @param datetime $publish_date the date it should be published
	 * @param int $product_id the product has already been created, so use it's id
	 * @return bool false if failed to create to cart
	 */
	public function update( $name, $slug, $description, $status, $sku, $price, $list_price, $product_specifications, $brand_id, $industry_id, $publish_visibility, $publish_date, $product_id, $weight = 0, $volume = 0 ) {
		// Assign local variable
		$this->product_id = $product_id;
		
		$product_specs = array();
		
		$ps_array = explode( '|', stripslashes( $product_specifications ) );
		
		// serialize product specificatons
		foreach ( $ps_array as $ps ) {
			if ( '' != $ps ) {
				list( $spec_name, $spec_value, $sequence ) = explode( '`', $ps );
				$product_specs[] = array( $spec_name, $spec_value, $sequence );
			}
		}

		if ( empty( $list_price ) || 'List Price (Optional)' == $list_price )
			$list_price = 0;
		
		$this->db->update( 'products', array(
				'brand_id' => $brand_id,
				'industry_id' => $industry_id,
				'name' => $name,
				'slug' => $slug,
				'description' => $description,
				'status' => $status,
				'sku' => trim( $sku ),
				'price' => $price,
				'list_price' => $list_price,
				'weight' => $weight,
				'volume' => $volume,
				'product_specifications' => serialize( $product_specs ),
				'publish_visibility' => $publish_visibility,
				'publish_date' => $publish_date,
				'user_id_modified' => ( ( isset( $_SESSION['user']['user_id'] ) ) ? $_SESSION['user']['user_id'] : 353 ),
			), array( 'product_id' => $product_id ), 'iisssssdddisssi', 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Removes website products
	 *
	 * @param int $product_id
	 * @param object $c Categories class
	 * @return string a list of website_ids (CSV)
	 */
	public function remove_product( $product_id, $c ) {		
		// Typecast
		$product_id = (int) $product_id;
		
		$website_results = $this->db->get_col( "SELECT `website_id` FROM `website_products` WHERE `product_id` = $product_id AND `active` = 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website results.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( !$website_results || empty( $website_results ) )
			return true;
			
		// Don't call a function if its blank
		if ( count( $website_results ) > 0 ) {
			$website_ids = implode( ',', $website_results );
						
			// Set all the products as inactive
			$this->db->query( "UPDATE `website_products` SET `active` = 0 WHERE `product_id` = $product_id AND `website_id` IN ($website_ids)" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to delete website product.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Get the product categories
		$category_ids = $this->get_product_categories( $product_id );
		
		if ( !$category_ids )
			return false;
		
		// @Fix there should be a more efficient way then a double loop with sql queries
		foreach ( $category_ids as $cid ) {
			$parent_categories = $c->get_parent_category_ids( $cid );
			
			// Delete parent categories if the website doesn't have any products
			foreach ( $parent_categories as $pc_id ) {
				$websites_without_products = $this->websites_without_products( $pc_id, $website_ids, $c );
				
				if ( empty( $websites_without_products ) )
					continue;
				
				$this->db->query( "DELETE FROM `website_categories` WHERE `website_id` IN($websites_without_products) AND `category_id` = $pc_id" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to deleted website categories.', __LINE__, __METHOD__ );
					return false;
				}
			}
			
			$websites_without_products = $this->websites_without_products( $cid, $website_ids, $c );
			
			if ( empty( $websites_without_products ) )
				continue;
			
			$this->db->query( "DELETE FROM `website_categories` WHERE `website_id` IN($websites_without_products) AND `category_id` = $cid" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to deleted parent website categories.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return $website_ids;
	}
	
	/**
	 * Add Products
	 *
	 * @param int $product_id
	 * @param string $website_ids CSV list website ids
	 * @param array $categories_array an array of category ids to add
	 * @param object $c Categories class
	 * @return
	 */
	 public function add_product( $product_id, $website_ids, $categories_array, $c ) {
		if ( empty( $website_ids ) )
			return true;
		
		$this->db->query( "UPDATE `website_products` SET `active` = 1 WHERE `website_id` IN($website_ids) AND `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website products.', __LINE__, __METHOD__ );
			return false;
		}

		/**
		 * Check if category do not exists insert it
		 */
		foreach ( $categories_array as $category_id ) {
			if ( empty( $category_id ) )
				continue;
			
			// @Fix don't think we need to do this each time
			$category_results = $this->db->get_results( "SELECT `website_id`, `category_id` FROM `website_categories` WHERE `website_id` IN($website_ids)", ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get website categories.', __LINE__, __METHOD__ );
				return false;
			}
			
			$category_ids = array();
			
			// Create an array having the categories based on the website
			if ( is_array( $category_results ) )
			foreach ( $category_results as $cr ) {
				$category_ids[$cr['website_id']][] = $cr['category_id'];
			}
			
			$website_ids_array = array_keys( $category_ids );
			$website_without_category_ids = array();
			$website_without_parent_category_ids = array();
			$parent_categories = $c->get_parent_category_ids( $category_id );
			
			// Cycle through the websites and findout if they already have the category
			foreach ( $website_ids_array as $wid ) {
				if ( !is_array( $category_results ) || !in_array( $category_id, $category_ids[$wid] ) ) {
					$website_without_category_ids[] = $wid;
					
					// Add the parent category_ids
					foreach ( $parent_categories as $cat ) {
						if ( !in_array( $cat, $category_ids[$wid] ) )
							$website_without_parent_category_ids[$cat][] = $wid;
					}
				}
			}
			
			// Check to see if any websites need to add their categories
			if ( count( $website_without_category_ids ) > 0 ) {
				$parent_categories = $c->get_parent_category_ids( $category_id );
				
				// Add the main category
				$this->add_product_category( $product_id, $category_id, $website_without_category_ids );
				
				// Foreach parent categories
				$without_parent_category_ids = array_keys( $website_without_parent_category_ids );
				
				
				// @Fix this is a looped sql statement
				if ( is_array( $without_parent_category_ids ) && count( $without_parent_category_ids ) > 0 )
				foreach ( $without_parent_category_ids as $wpci ) {
					if ( is_array( $website_without_parent_category_ids[$wpci] ) )
						$this->add_product_category( $product_id, $wpci, $website_without_parent_category_ids[$wpci] );
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Adds a product image to a product
	 *
	 * @param array $images
	 * @return bool
	 */
	public function add_product_images( $images, $product_id ) {
		// Typecast
		$product_id = (int) $product_id;
		
		// Initiate values
		$values = '';
		
		// No images to work with
		if ( !is_array( $images ) )
			return true;
		
		foreach ( $images as $key => $image ) {
			// Putting the definition of $sequence down below (after the list() statement) made it actually not assign zero.  Putting it up here too.
			$sequence = 0;
			
			if ( preg_match( '/^\//', $image ) == 1 )
				$image = substr( $image, 1 );
	
			// Get it's sequence
			if ( stristr( $image, '|' ) )
				list( $image, $sequence ) = explode( '|', $image );
				
			// Give it a value if it was empty
			if ( empty( $sequence ) )
				$sequence = 0;
			
			
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $product_id, '" . $this->db->escape( $image ) . "', " . (int) $sequence . ' )';
		}
		
		if ( empty( $values ) )
			return true;
		
		$this->db->query( "INSERT INTO `product_images` ( `product_id`, `image`, `sequence` ) VALUES $values" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add product images.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Adds a product category
	 *
	 * @param int $product_id
	 * @param int $category_id
	 * @param array $website_ids
	 * @return bool
	 */
	private function add_product_category( $product_id, $category_id, $website_ids ) {
		
		// Insert a new website category
		$image = $this->db->get_var( "SELECT `image` FROM `product_images` WHERE `product_id` = $product_id AND `sequence` = 0 LIMIT 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product image.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Instantiate new class
		$i = new Industries;
		
		$image_url = 'http://' . $i->get_by_product( $product_id ) . '.retailcatalog.us/products/' . $product_id . '/' . $image;
		
		$values = '';
		
		foreach ( $website_ids as $wid ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $wid, $category_id, '$image_url' )";
		}
		
		$this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES " . $values );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add website category.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Adds product categories
	 *
	 * @param int $product_id
	 * @param array $categories
	 * @return bool
	 */
	public function add_categories( $product_id, $categories ) {
		// Typecast
		$product_id = (int) $product_id;
		
		// Set initial values
		$values = '';
		
		if ( is_array( $categories ) )
		foreach ( $categories as $cid ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $product_id, " . (int) $cid . ')';
		}
		
		if ( empty( $values ) )
			return true;
		
		$this->db->query( "INSERT INTO `product_categories` (`product_id`, `category_id`) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets a product's categories
	 *
	 * @param int $product_id
	 * @return array
	 */
	private function get_product_categories( $product_id ) {
		$category_ids = $this->db->get_col( "SELECT `category_id` FROM `product_categories` WHERE `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get update website product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $category_ids;
	}
	
	/**
	 * Websites without products
	 *
	 * @param int $category_id
	 * @param array $website_ids
	 * @return array
	 */
	private function websites_without_products( $category_id, $website_ids, $c ) {
		$categories = $c->get_sub_category_ids( $category_id );
		$categories[] = $category_id;
		
		// @Fix shouldn't need to do the count
		$website_result_array = $this->db->get_results( "SELECT a.`website_id`, COUNT(*) AS product_count FROM `website_products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`active` = 1 AND a.`website_id` IN($website_ids) AND b.`category_id` IN(" . implode( ',', $categories ) . ') GROUP BY `website_id` HAVING product_count > 0', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get websites without products.', __LINE__, __METHOD__ );
			return false;
		}
		
		$website_with_products = array();
		
		// This all the websites with products
		if ( is_array( $website_result_array ) )
        foreach ( $website_result_array as $row ) {
			$website_with_products[] = $row['website_id'];
		}
		
		// All the original websites
		$all_website_ids = explode( ',', $website_ids );
		$websites_without_products = '';
		
		foreach ( $all_website_ids as $wid ) {
			if ( in_array( $wid, $website_with_products ) )
				continue;
			
			if ( !empty( $websites_without_products ) )
				$websites_without_products .= ',';
			
			$websites_without_products .= $wid;
		}
		
		return $websites_without_products;
	}
	
	/**
	 * Empty product categories for a specific product ID
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function empty_categories( $product_id ) {
		$this->db->query( 'DELETE FROM `product_categories` WHERE `product_id` = ' . (int) $product_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get delete product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Empty product images for a specific product ID
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function empty_product_images( $product_id ) {
		$this->db->query( 'DELETE FROM `product_images` WHERE `product_id` = ' . (int) $product_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get delete product images.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Lists all the products
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_products( $where, $order_by, $limit ) {
		$products = $this->db->get_results( "SELECT a.`product_id`, a.`name`, d.`name` AS brand, a.`sku`, a.`status`, DATE( a.`publish_date` ) AS publish_date, c.`name` AS category, e.`image`, e.`swatch` FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON (a.brand_id = d.brand_id) LEFT JOIN `product_images` AS e ON (a.`product_id` = e.`product_id`) WHERE ( e.`sequence` = 0 OR e.`sequence` IS NULL ) $where GROUP BY a.`product_id` ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Counts all the products
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_products( $where ) {
		// @Fix shouldn't need to do the count function
		// Get the product count
		$product_count = $this->db->get_col( "SELECT a.`product_id` FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON (a.brand_id = d.brand_id) LEFT JOIN `product_images` AS e ON (a.`product_id` = e.`product_id`) WHERE ( e.`sequence` = 0 OR e.`sequence` IS NULL ) $where GROUP BY a.`product_id`" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return count( $product_count );
	}
	
	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
	 * @param string $as (optional|) if you want the field to come out as another name
	 * @param string $where (optional|) any where statement
	 * @return bool
	 */
	public function autocomplete( $query, $field, $as = '', $where = '' ) {
		if ( !empty( $as ) )
			$as = " AS $as";
		
		// Get results
		$results = $this->db->prepare( "SELECT DISTINCT( $field )$as FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON (a.brand_id = d.brand_id) LEFT JOIN `product_images` AS e ON (a.`product_id` = e.`product_id`) WHERE e.`sequence` = 0 AND $field LIKE ? $where GROUP BY a.`product_id` ORDER BY $field LIMIT 10", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocomplete entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Changes a product industry
	 *
	 * @param int $product_id
	 * @param int $industry_id
	 * @return bool
	 */
	public function change_industry( $product_id, $industry_id ) {
		$this->db->update( 'products', array( 'industry_id' => $industry_id ), array( 'product_id' => $product_id ), 'i', 'i' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to change product industry.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Clones a product
	 *
	 * @param int $product_id
	 * @return int
	 */
	public function clone_product( $product_id ) {
		global $user;
		
		// Type Juggling
		$product_id = (int) $product_id;
		$user_id = (int) $user['user_id'];
		
		// Make sure it's a real product
		$exists = $this->db->get_var( "SELECT `product_id` FROM `products` WHERE `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check if product exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Check to see if it exists
		if ( !$exists )
			return false;
		
		// Clone product
		$this->db->query( "INSERT INTO `products` ( `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, `user_id_created`, `date_created` ) SELECT `brand_id`, `industry_id`, CONCAT( `name`, ' (Clone)' ), CONCAT( `slug`, '-2' ), `description`, `status`, CONCAT( `sku`, '-2' ), `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, $user_id, NOW() FROM `products` WHERE `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to clone product.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the new product ID
		$new_product_id = $this->db->insert_id;
		
		// Clone categories
		$this->db->query( "INSERT INTO `product_categories` ( `product_id`, `category_id` ) SELECT $new_product_id, `category_id` FROM `product_categories` WHERE `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to clone product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Clone product groups
		$this->db->query( "INSERT INTO `product_group_relations` ( `product_group_id`, `product_id` ) SELECT `product_group_id`, $new_product_id FROM `product_group_relations` WHERE `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to clone product group relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Clone tags
		$this->db->query( "INSERT INTO `tags` ( `object_id`, `type`, `value` ) SELECT $new_product_id, 'product', `value` FROM `tags` WHERE `object_id` = $product_id AND `type` = 'product'" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to clone product tags.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Clone attributes items
		$this->db->query( "INSERT INTO `attribute_item_relations` ( `attribute_item_id`, `product_id` ) SELECT `attribute_item_id`, $new_product_id FROM `attribute_item_relations` WHERE `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to clone product attribute item relations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $new_product_id;
	}
	
	/**
	 * Removes a product image
	 *
	 * @param string $image
	 * @param int $product_id
	 * @return bool
	 */
	public function remove_image( $image, $product_id ) {
		$this->db->prepare( 'DELETE FROM `product_images` WHERE `image` = ? AND `product_id` = ?', 'si', $image, $product_id )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete product image.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Sets a product as inactive
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function delete( $product_id ) {
		$this->db->update( 'products', array( 'publish_visibility' => 'deleted' ), array( 'product_id' => $product_id ), 's', 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get old ashley product id
	 *
	 * @return int
	 */
	public function get_old_ashley_product_id() {
		$product_id = $this->db->get_var( "SELECT a.`product_id` FROM `products` AS a LEFT JOIN `brands` AS b ON ( a.`brand_id` = b.`brand_id` ) WHERE a.`user_id_created` <> 353 AND a.`publish_visibility` <> 'deleted' AND b.`name` LIKE 'Ashley Furniture%' LIMIT 1" );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get old ashley product id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_id;
	}

	/**
	 * Get the new ashley product id
	 *
	 * @return int
	 */
	public function get_new_ashley_product_id( $old_product_id ) {
		// Type Juggling
		$old_product_id = (int) $old_product_id;
		
		$product_id = $this->db->get_var( "SELECT a.`product_id` FROM `products` AS a LEFT JOIN `products` AS b ON ( a.`sku` = b.`sku` ) LEFT JOIN `brands` AS c ON ( b.`brand_id` = c.`brand_id` ) WHERE a.`user_id_created` = 353 AND c.`name` LIKE 'Ashley Furniture%' AND b.`publish_visibility` <> 'deleted' AND b.`user_id_created` <> 353 AND b.`product_id` = $old_product_id LIMIT 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get new ashley product id.', __LINE__, __METHOD__ );
			return false;
		}
		
		// If success, return it
		if ( $product_id > 0 )
			return $product_id;
		
		// If failure, return it
		$product_id = $this->db->get_var( "SELECT a.`product_id` FROM `products` AS a LEFT JOIN `products` AS b ON ( a.`slug` = b.`slug` ) LEFT JOIN `brands` AS c ON ( b.`brand_id` = c.`brand_id` ) WHERE a.`user_id_created` = 353 AND c.`name` LIKE 'Ashley Furniture%' AND b.`publish_visibility` <> 'deleted' AND b.`user_id_created` <> 353 AND b.`product_id` = $old_product_id LIMIT 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get new ashley product id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_id;
	}
	
	/**
	 * Autocomplete new ashley products
	 *
	 * @param string $term
	 * @return array
	 */
	public function autocomplete_new_ashley( $term ) {
		$suggestions = $this->db->prepare( "SELECT `product_id`, CONCAT( `sku`, ' - ', `name` ) AS name, CONCAT( `sku`, ' - ', `name` ) AS value FROM `products` WHERE `user_id_created` = 353 AND `publish_visibility` = 'public' AND `status` <> 'discontinued' AND `sku` LIKE ? LIMIT 10", 's', $term . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocomplete for new ashley products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Replace products
	 *
	 * @param int $old_product_id
	 * @param int $new_product_id
	 * @return int
	 */
	public function replace_product( $old_product_id, $new_product_id ) {
		$this->db->prepare( 'UPDATE `website_products` SET `product_id` = ? WHERE `product_id` = ?', 'ii', $old_product_id, $new_product_id )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to replace product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->rows_affected;
	}
	
	/**
	 * Get websites products list (the websites that have a product)
	 *
	 * @param int $product_id
	 * @return int
	 */
	public function get_websites_related_to_product( $product_id ) {
		// Type Juggling
		$product_id = (int) $product_id;
		
		$websites = $this->db->get_col( "SELECT a.`title` FROM `websites` AS a LEFT JOIN `website_products` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get websitse related to a product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $websites;
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