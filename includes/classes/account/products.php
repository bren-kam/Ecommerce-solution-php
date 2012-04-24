<?php
/**
 * Handles all the Products
 *
 * @package Grey Suit Retail
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
	 * @return int
	 */
	public function create() {
		global $user;
		
		$this->db->insert( 'products', array( 'website_id' => $user['website']['website_id'], 'user_id_created' => $user['user_id'], 'publish_visibility' => 'deleted', 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Add Products
	 *
	 * @param array $products
	 * @return  bool
	 */
	public function add_products( array $products ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// If there are no products to add, then don't do anything
		if ( count( $products ) <= 0 )
			return false;
		
		// Create values
		$values = '';
		
		foreach ( $products as &$product_id ) {
			$product_id = (int) $product_id;
			
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $website_id, $product_id )";
		}
		
		// Insert website products
		$this->db->query( "INSERT INTO `website_products` ( `website_id`, `product_id` ) VALUES $values ON DUPLICATE KEY UPDATE `active` = 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add website products.', __LINE__, __METHOD__ );
			return false;
		}

		$product_ids = implode( ',', $products );
		
		// Get category IDs
		$category_ids = $this->db->get_col( "SELECT DISTINCT `category_id` FROM `product_categories` WHERE `product_id` IN ($product_ids)" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there are any categories that need to be added
		if ( !empty( $category_ids ) ) {
			// Need to get the parent categories
			$c = new Categories;
				
			$parent_category_ids = $used_parent_category_ids = array();
			
			foreach ( $category_ids as $cid ) {
				$parent_category_ids[$cid] = $c->get_parent_category_ids( $cid );
			}
			
			$category_images = $this->db->get_results( "SELECT a.`category_id`, CONCAT( 'http://', c.`name`, '.retailcatalog.us/products/', b.`product_id`, '/', d.`image` ) FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `industries` AS c ON ( b.`industry_id` = c.`industry_id` ) LEFT JOIN `product_images` AS d ON ( b.`product_id` = d.`product_id` ) LEFT JOIN `website_categories` AS e ON ( a.`category_id` = e.`category_id` AND e.`website_id` = $website_id) WHERE a.`category_id` IN(" . implode( ',', $category_ids ) . ") AND b.`product_id` IN( $product_ids ) AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND d.`sequence` = 0 AND e.`category_id` IS NULL GROUP BY a.`category_id`", ARRAY_A );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get website category images.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Create insert
			$values = '';
			$category_images = ar::assign_key( $category_images, 'category_id', true );
			
			foreach ( $category_ids as $cid ) {
				if ( !empty( $values ) )
					$values .= ',';
				
				// This image will be used for the parent categories as well
				$image = $this->db->escape( $category_images[$cid] );
				$values .= "( $website_id, $cid, '$image' )";
				
				foreach ( $parent_category_ids[$cid] as $pcid ) {
					// Don't set the same parent category twice
					if ( in_array( $pcid, $used_parent_category_ids ) )
						continue;
					
					if ( !empty( $values ) )
						$values .= ',';
					
					$values .= "( $website_id, $pcid, '$image' )";
					
					// Add it to the list
					$used_parent_category_ids[] = $pcid;
				}
			}
			
			// Add the values
			if ( !empty( $values ) ) {
				$this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES $values ON DUPLICATE KEY UPDATE `category_id` = VALUES( `category_id` )" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to add website categories.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Removes a product from a website
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function remove( $product_id ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
	
		$this->db->update( 'website_products', array( 'active' => 0 ), array( 'product_id' => $product_id, 'website_id' => $user['website']['website_id'] ), 'i', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to remove product.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Instantiate Class
		$c = new Categories;
		
		// Get Categories
		$category_ids = $this->get_product_categories( $product_id );
		
		// Check each of the parent categories
		$parent_categories = $delete_category_ids = array();
		
		// @Fix should not loop categories
		foreach ( $category_ids as $cid ) {
			$parent_categories = array_merge( $parent_categories, $c->get_parent_category_ids( $cid ) );
				
			// Delete parent categories
			foreach ( $parent_categories as $pc_id ) {
				if ( !$this->has_products( $pc_id, $c ) )
					$delete_category_ids[] = $pc_id;
			}
			
			// Check if we have to delete the category
			if ( !$this->has_products( $cid, $c ) )
				$delete_category_ids[] = $cid;
		}
		
		if ( count( $delete_category_ids ) > 0 ) {
		
			// Delete the categories that need to be deleted
			$this->db->query( "DELETE FROM `website_categories` WHERE `website_id` = $website_id AND `category_id` IN(" . preg_replace( '/[^0-9,]/', '', implode( ',', $delete_category_ids ) ) . ')' );
	
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to delete website categories.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Checks to see if a category has products
	 *
	 * @param int $category_id
	 * @param object $c Categories class
	 * @return bool
	 */
	public function has_products( $category_id, $c ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// @Fix should cache results of this so that if it's run many times the same categories are not checked
		$categories = $c->get_sub_category_ids( $category_id );
		$categories[] = $category_id;
		
		$count = $this->db->get_var( "SELECT COUNT( a.`product_id` ) FROM `website_products` AS a LEFT JOIN `product_categories` AS b ON (a.`product_id` = b.`product_id`) WHERE a.`active` = 1 AND a.`website_id` = $website_id AND b.`category_id` IN(" . preg_replace( '/[^0-9,]/', '', implode( ',', $categories ) ) . ')' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check if the category has any products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count > 0;
	}
	
	/**
	 * Updates the product parameters for product of a specific website
	 *
	 * @param int $product_id
	 * @param array $product_values
	 * @param array $coupons
	 * @param array $product_options
	 * @return bool
	 */
	public function update_product( $product_id, $product_values, $coupons, $product_options ) {
		global $user;
		
		// Type Juggling
		$product_id = (int) $product_id;
		$website_id = (int) $user['website']['website_id'];
				
		// Determine what everything is
		$strings = array( 'additional_shipping_type', 'alternate_price_name', 'meta_title', 'meta_description', 'meta_keywords', 'protection_type', 'price_note', 'product_note', 'ships_in', 'store_sku', 'warranty_length' );
		$floats = array( 'alternate_price', 'price', 'sale_price', 'wholesale_price', 'additional_shipping_amount', 'protection_amount', 'weight' );
		$integers = array( 'inventory', 'display_inventory', 'on_sale', 'status' );
		$field_types = '';
		
		// Create fieldsa rray
		foreach ( $product_values as $k => $v ) {
			// Get the type
			if ( in_array( $k, $strings ) ) {
				$field_types .= 's';

                // Need to make sure we don't have any white spaces
                $v = trim( $v );
			} elseif ( in_array( $k, $floats ) ) {
				$field_types .= 'd';
			} elseif ( in_array( $k, $integers ) ) {
				$field_types .= 'i';
			} else {
				continue;
			}
			
			$fields[$k] = $v;
		}
		
		
		$this->db->update( 'website_products', $fields, array( 'product_id' => $product_id, 'website_id' => $website_id ), $field_types, 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete coupons first
		$this->db->query( "DELETE a.* FROM `website_coupon_relations` AS a LEFT JOIN `website_coupons` AS b ON ( a.`website_coupon_id` = b.`website_coupon_id` ) WHERE a.`product_id` = $product_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website coupons.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add new coupons
		if ( $coupons ) {
			$website_coupons = $this->website_coupons();
			$coupon_values = '';
			
			foreach ( $coupons as $website_coupon_id ) {
				// Make sure it's owned by this website
				if ( !in_array( $website_coupon_id, $website_coupons ) )
					continue;
				
				if ( !empty( $coupon_values ) )
					$coupon_values .= ', ';
				
				$coupon_values .= '( ' . (int) $website_coupon_id . ", $product_id )";
			}
			
			$this->db->query( "INSERT INTO `website_coupon_relations` ( `website_coupon_id`, `product_id` ) VALUES $coupon_values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to add new coupons.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Can we delete from both tables at once? Do website_id and product id need to be in both fields? 
		
		// Delete product options list items
		$this->db->query( "DELETE FROM `website_product_option_list_items` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to website product option list items.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete product options
		$this->db->query( "DELETE FROM `website_product_options` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website product options.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add new product options
		if ( $product_options ) {
			$product_option_values = $product_option_list_item_values = $product_option_ids = $product_option_list_item_ids = '';
			
			foreach ( $product_options as $po_id => $po ) {
				$dropdown = is_array( $po );
				
				if ( $dropdown ) {
					$price = 0;
					$required = $po['required'];
				} else {
					$price = $po;
					$required = 0;
				}
				
				if ( !empty( $product_option_values ) )
					$product_option_values .= ', ';
				
				if ( !empty( $product_option_ids ) )
					$product_option_ids .= ', ';
				
				// Add the values
				$product_option_values .= sprintf( "( $website_id, $product_id, %d, %f, %d )", $po_id, $price, $required );
				
				// For error handling
				$product_option_ids .= $po_id;
								
				// If it's a drop down, set the values
				if ( $dropdown )
				foreach ( $po['list_items'] as $li_id => $price ) {
					if ( !empty( $product_option_list_item_values ) )
						$product_option_list_item_values .= ',';

					if ( !empty( $product_option_list_item_ids ) )
						$product_option_list_item_ids .= ',';
					
					$product_option_list_item_values .= sprintf( "( $website_id, $product_id, %d, %d, %f )", $po_id, $li_id, $price );
				}
			}
			
			// Insert new product options
			$this->db->query( "INSERT INTO `website_product_options` ( `website_id`, `product_id`, `product_option_id`, `price`, `required` ) VALUES $product_option_values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to add new product options.', __LINE__, __METHOD__ );
				return false;
			}
			
			if ( $product_option_list_item_values != '' ) {
				// Insert new product option list items
				$this->db->query( "INSERT INTO `website_product_option_list_items` ( `website_id`, `product_id`, `product_option_id`, `product_option_list_item_id`, `price` ) VALUES $product_option_list_item_values" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to add new product option list items.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Gets a product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product( $product_id ) {
		// Type Juggling
		$product_id = (int) $product_id;

		$product = $this->db->get_row( "SELECT a.`product_id`, a.`name`, a.`slug`, a.`description`, a.`product_specifications`, d.`name` AS brand, a.`sku`, a.`status`, c.`category_id`, c.`name` AS category, e.`image`, f.`name` AS industry FROM `products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` ) INNER JOIN `product_images` AS e ON (a.`product_id` = e.`product_id`) LEFT JOIN `industries` AS f ON ( a.`industry_id` = f.`industry_id` ) WHERE a.`product_id` = $product_id AND e.`sequence` = 0", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product;
	}
	
	/**
	 * Get all products
	 *
	 * @return array
	 */
	public function get_all_products() {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$products = $this->db->get_results( "SELECT b.`sku`, b.`name`, d.`name` AS category, e.`name` AS brand FROM `website_products` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `product_categories` AS c ON ( b.`product_id` = c.`product_id` ) LEFT JOIN `categories` AS d ON ( c.`category_id` = d.`category_id` ) LEFT JOIN `brands` AS e ON ( b.`brand_id` = e.`brand_id` ) WHERE a.`website_id` = $website_id AND a.`status` > 0 AND a.`active` = 1 AND b.`publish_visibility` = 'public' GROUP BY a.`product_id`", ARRAY_A );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get all products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Gets a custom product
	 *
	 * @param int $product_id the id of the product
	 * @return array
	 */
	public function get_custom_product( $product_id ) {
		global $user;
		
		// Type Juggling
		$product_id = (int) $product_id;
		$website_id = (int) $user['website']['website_id'];
		
		$product = $this->db->get_row( "SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`name`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`price`, a.`weight`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, b.`name` AS industry  FROM `products` AS a INNER JOIN `industries` AS b ON (a.`industry_id` = b.`industry_id`) WHERE a.`product_id` = $product_id AND a.`website_id` = $website_id", ARRAY_A );
		
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
		
		if ( is_array( $product_images ) )
		foreach ( $product_images as $image ) {
			$images[$image['swatch']][] = $image['image'];
		}
		
		return $images;
	}

    /**
	 * Retrieves the partial URLs of all images for a given Product_id
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product_image_urls( $product_id ) {
		// Type Juggling
		$product_id = (int) $product_id;

		$images = $this->db->get_col( "SELECT CONCAT( 'http://', b.`name`, '.retailcatalog.us/products/', c.`product_id`, '/', a.`image` ) AS image_url FROM `product_images` AS a LEFT JOIN `products` AS c ON (a.`product_id` = c.`product_id`) LEFT JOIN `industries` AS b ON (c.`industry_id` = b.`industry_id`) WHERE a.`product_id` = $product_id ORDER BY a.`sequence` ASC LIMIT 5" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get product image urls.', __LINE__, __METHOD__ );
			return false;
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
	 * Gets a website product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_website_product( $product_id ) {
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $product_id = (int) $product_id;

		$website_product = $this->db->get_row( "SELECT a.`product_id`, a.`name`, d.`name` AS brand, a.`sku`, c.`name` AS category, e.`image`, f.`name` AS industry, g.`price` FROM `products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` ) LEFT JOIN `product_images` AS e ON (a.`product_id` = e.`product_id`) LEFT JOIN `industries` AS f ON ( a.`industry_id` = f.`industry_id` ) LEFT JOIN `website_products` AS g ON ( a.`product_id` = g.`product_id` ) WHERE e.`sequence` = 0 AND g.`status` > 0 AND a.`product_id` = $product_id AND g.`website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_product;
	}
	
	/**
	 * Gets specific information from the website_products table
	 *
	 * @param int $product_id
	 * @return associative array/bool
	 */
	public function get_complete_website_product( $product_id ) {
		global $user;
		
		// Type Juggling
		$product_id = (int) $product_id;
		$website_id = (int) $user['website']['website_id'];
		
		$website_product = $this->db->get_row( "SELECT `product_id`, `alternate_price`, `price`, `sale_price`, `wholesale_price`, `inventory`, `additional_shipping_amount`, `weight`, `alternate_price_name`, `protection_amount`, `additional_shipping_type`, `meta_title`, `meta_description`, `meta_keywords`, `protection_type`, `price_note`, `product_note`, `ships_in`, `store_sku`, `warranty_length`, `display_inventory`, `on_sale`, `status` FROM `website_products` WHERE `product_id` = $product_id AND `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get complete website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		$website_product['coupons'] = false;
		$website_product['product_options'] = false;
		
		// Get coupons
		$coupons = $this->db->get_results( "SELECT a.`website_coupon_id`, a.`name` FROM `website_coupons` AS a LEFT JOIN `website_coupon_relations` AS b ON ( a.`website_coupon_id` = b.`website_coupon_id` ) WHERE a.`website_id` = $website_id AND b.`product_id` = $product_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website coupons.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( $coupons )
			$website_product['coupons'] = ar::assign_key( $coupons, 'website_coupon_id', true );
		
		// Get product options with list items
		$product_options1 = $this->db->get_results( "SELECT a.`product_option_id`, b.`product_option_list_item_id`, b.`value`, c.`price`, c.`required`, d.`price` AS list_item_price FROM `product_options` AS a LEFT JOIN `product_option_list_items` AS b ON ( a.`product_option_id` = b.`product_option_id` ) INNER JOIN `website_product_options` AS c ON ( a.`product_option_id` = c.`product_option_id` ) INNER JOIN `website_product_option_list_items` AS d ON ( c.`product_option_id` = d.`product_option_id` AND b.`product_option_list_item_id` = d.`product_option_list_item_id` AND c.`product_id` = d.`product_id` AND d.`website_id` = $website_id ) WHERE c.`website_id` = $website_id AND c.`product_id` = $product_id AND ( a.`option_type` = 'checkbox' OR a.`option_type` = 'select' AND d.`price` IS NOT NULL ) GROUP BY d.`product_option_list_item_id` ORDER BY b.`sequence` DESC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product options.', __LINE__, __METHOD__ );
			return false;
		}		
		
		// @Fix - have to do this to grab the non-list ones.  There's a better way, I'm sure...
		$product_options2 = $this->db->get_results( "SELECT a.`option_type`, a.`product_option_id`, c.`price`, c.`required` FROM `product_options` AS a LEFT JOIN `website_product_options` AS c ON ( a.`product_option_id` = c.`product_option_id` )  WHERE c.`website_id` = $website_id AND c.`product_id` = $product_id AND ( ( c.`price` != 0 AND c.`price` IS NOT NULL ) || ( a.`option_type` = 'text' ) ) GROUP BY c.`product_option_id`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product options.', __LINE__, __METHOD__ );
			return false;
		}		
		
		// Coalesce them
		if ( !empty( $product_options1 ) ) {
			if ( !empty ( $product_options2 ) ) {
				$product_options = array_merge( $product_options1, $product_options2 );
			} else {
				$product_options = $product_options1;
			}
		} else {
			if ( !empty( $product_options2 ) ) {
				$product_options = $product_options2;
			} else {
				$product_options = array();
			}
		}
		
		// @Fix seems there is a more efficient way to do this
		if ( $product_options )
		foreach ( $product_options as $po ) {
			$website_product['product_options'][$po['product_option_id']]['price'] = $po['price'];
			$website_product['product_options'][$po['product_option_id']]['required'] = $po['required'];
			$website_product['product_options'][$po['product_option_id']]['list_items'][$po['product_option_list_item_id']] = $po['list_item_price'];
		}
		
		return $website_product;
	}
	
	/**
	 * Get brand product_options
	 *
	 * @param int $product_id
	 * @return array
	 */
	 public function brand_product_options( $product_id ) {
		// Type Juggling
		$product_id = (int) $product_id;
		
		$product_options_array = $this->db->get_results( "SELECT a.`product_option_id`, a.`option_type`, a.`option_name`, b.`product_option_list_item_id`, b.`value` FROM `product_options` AS a LEFT JOIN `product_option_list_items` AS b ON ( a.`product_option_id` = b.`product_option_id` ) LEFT JOIN `product_option_relations` AS c ON ( a.`product_option_id` = c.`product_option_id` ) LEFT JOIN `products` AS d ON ( c.`brand_id` = d.`brand_id` ) WHERE d.`product_id` = $product_id ORDER BY b.`sequence`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get brand product options.', __LINE__, __METHOD__ );
			return false;
		}
		
		$product_options = false;
		
		if ( $product_options_array )
		foreach ( $product_options_array as $po ) {
			$product_options[$po['product_option_id']]['option_type'] = $po['option_type'];
			$product_options[$po['product_option_id']]['option_name'] = $po['option_name'];
			$product_options[$po['product_option_id']]['list_items'][$po['product_option_list_item_id']] = $po['value'];
		}
		
		return $product_options;
	}

	/**
	 * Get Products By Ids
	 *
	 * @param array $product_ids
	 * @return array
	 */
	public function get_products_by_ids( $product_ids ) {
		if ( !is_array( $product_ids ) || count( $product_ids ) < 1 )
			return array();
		
		global $user;
		
		$product_ids_string = '';
		
		// Make sure its a safe string
		foreach ( $product_ids as $pid ) {
			if ( !empty( $product_ids_string ) )
				$product_ids_string .= ',';
			
			$product_ids_string .= (int) $pid;
		}
		
		$products = $this->db->get_results( "SELECT a.`product_id`, a.`name`, a.`slug`, d.`name` AS brand, a.`sku`, a.`status`, c.`category_id`, c.`name` AS category, e.`image`, f.`name` AS industry FROM `products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `categories` AS c ON ( b.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` ) INNER JOIN `product_images` AS e ON ( a.`product_id` = e.`product_id` ) LEFT JOIN `industries` AS f ON ( a.`industry_id` = f.`industry_id` ) WHERE ( a.`website_id` = 0 OR a.`website_id` = " . (int) $user['website']['website_id'] . " ) AND e.`sequence` = 0 AND a.`product_id` IN ($product_ids_string) GROUP BY a.`product_id`", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Gets Products
	 *
	 * @param string $where (optional|)
	 * @return array
	 */
	public function get_products( $where = '' ) {
		global $user;
		
		$products = $this->db->get_results( "SELECT a.`product_id`, a.`name`, a.`slug`, d.`name` AS brand, a.`sku`, a.`status`, c.`category_id`, c.`name` AS category, e.`image`, f.`name` AS industry FROM `products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `categories` AS c ON ( b.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` ) INNER JOIN `product_images` AS e ON ( a.`product_id` = e.`product_id` ) LEFT JOIN `industries` AS f ON ( a.`industry_id` = f.`industry_id` ) WHERE ( a.`website_id` = 0 OR a.`website_id` = " . (int) $user['website']['website_id'] . " ) AND e.`sequence` = 0 {$where} GROUP BY a.`product_id`", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	// @Fix these next two functions are very similar to the "list_website_products" function & count, but more extensive -- should be redone?
	/**
	 * Gets Website Products
	 *
	 * @param int $limit (optional) the number of products to get
	 * @param string $where (optional) a 'WHERE' clause to add on to the SQL Statement
     * @param int $page
	 * @return array products
	 */
	 public function get_website_products( $limit = 20, $where = '', $page = 1 ) {
		global $user;
		
		// Instantiate Classes
		$c = new Categories;

		// Type Juggling
		$website_id = (int) $user['website']['website_id'];

        if ( 0 == $limit ) {
            $sql_limit = '';
        } else {
            $starting_product = ( $page - 1 ) * $limit;
            $sql_limit = "LIMIT $starting_product, $limit";
        }

		$sql = 'SELECT a.`product_id`,';
		$sql .= 'a.`name`, a.`slug`, d.`name` AS brand, a.`sku`, a.`status`, c.`category_id`,';
		$sql .= 'c.`name` AS category, e.`image`, e.`swatch`, f.`price`, f.`alternate_price`, f.`alternate_price_name`,';
		$sql .= 'f.`sequence`, DATE( a.`publish_date` ) AS publish_date, e.`image`, e.`swatch`, g.`name` AS industry ';
		$sql .= 'FROM `products` AS a ';
		$sql .= 'LEFT JOIN `product_categories` AS b ON (a.`product_id` = b.`product_id`) ';
		$sql .= 'LEFT JOIN `categories` AS c ON (b.`category_id` = c.`category_id`) ';
		$sql .= 'LEFT JOIN `brands` AS d ON (a.`brand_id` = d.`brand_id`) ';
		$sql .= 'LEFT JOIN `product_images` AS e ON ( a.`product_id` = e.`product_id`) ';
		$sql .= 'LEFT JOIN `website_products` AS f ON (a.`product_id` = f.`product_id`) ';
		$sql .= 'LEFT JOIN `industries` AS g ON ( a.`industry_id` = g.`industry_id` ) ';
		$sql .= "WHERE f.`active` = 1 AND f.`website_id` = $website_id AND ( e.`sequence` = 0 OR e.`sequence` IS NULL ) AND a.`date_created` <> '0000-00-00 00:00:00' ";
		$sql .= $where;
		$sql .= " GROUP BY a.`product_id` ORDER BY f.`sequence` ASC $sql_limit";
		
		$products = $this->db->get_results( $sql, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website products.', __LINE__, __METHOD__ );
			return false;
		}

        if ( is_array( $products ) )
		foreach ( $products as &$p ) {
			$p['link'] = ( 0 == $p['category_id'] ) ? '/' . $p['slug'] : $c->category_url( $p['category_id'] ) . $p['slug'] . '/';
		}
		
		return $products;
	}
	
	/**
	 * Counts the website products
	 *
	 * @param string $where (optional|)
	 * @return int
	 */
	public function get_website_products_count( $where = ''  ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$sql = 'SELECT a.`product_id`';
		$sql .= 'FROM `products` AS a ';
		$sql .= 'LEFT JOIN `product_categories` AS b ON (a.`product_id` = b.`product_id`) ';
		$sql .= 'LEFT JOIN `categories` AS c ON (b.`category_id` = c.`category_id`) ';
		$sql .= 'LEFT JOIN `brands` AS d ON (a.`brand_id` = d.`brand_id`) ';
		$sql .= 'LEFT JOIN `product_images` AS e ON ( a.`product_id` = e.`product_id`) ';
		$sql .= 'LEFT JOIN website_products AS f ON (a.`product_id` = f.`product_id`) ';
		$sql .= 'LEFT JOIN `industries` AS g ON ( a.`industry_id` = g.`industry_id`) ';
		$sql .= "WHERE f.`active` = 1 AND f.`website_id` = $website_id AND e.`sequence` = 0 AND a.`date_created` <> '0000-00-00 00:00:00' ";
		$sql .= $where;
		$sql .= "GROUP BY a.`product_id` ORDER BY f.`sequence` ASC";
		
		$count = $this->db->get_col( $sql );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count website products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix shouldn't have to count this
		return count( $count );
	}
	
	/**
	 * Gets product IDs
	 *
	 * @param string $where (optional)
	 * @return array
	 */
	public function get_product_ids( $where = '' ) {
		$product_ids = $this->db->get_col( 'SELECT a.`product_id` FROM `products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `categories` AS c ON ( b.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` ) LEFT JOIN `website_products` AS e ON ( a.`product_id` = e.`product_id` ) WHERE 1 $where GROUP BY a.`product_id`', ARRAY_A );
				
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product ids.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product_ids;
	}
	
	/**
	 * Get Industry of a product
	 *
	 * @param int $product_id
	 * @return string
	 */
	public function get_industry( $product_id ) {
		$industry = $this->db->get_var( 'SELECT a.`name` FROM `industries` AS a LEFT JOIN `products` AS b ON ( a.`industry_id` = b.`industry_id` ) WHERE b.`product_id` = ' . (int) $product_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get industry.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industry;
	}
	
	/**
	 * Check weither SKU already exists
	 *
	 * @param string $sku
	 * @return array
	 */
	public function sku_exists( $sku ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Get the product if the SKU exists
		$product = $this->db->get_row( "SELECT a.`product_id`, a.`name`, IF( b.`product_id`, 1, 0 ) AS owned FROM `products` AS a LEFT JOIN `website_products` AS b ON ( a.`product_id` = b.`product_id` AND b.`website_id` = $website_id ) WHERE ( a.`website_id` = 0 || a.`website_id` = $website_id ) AND a.`sku` = '" . $this->db->escape( $sku ) . "' AND a.`publish_visibility` = 'public' AND a.`publish_date` <> '0000-00-00 00:00:00'", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check if SKU exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product;
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
		global $user;
		
		$product_specs = array();
		
		$ps_array = explode( '|', stripslashes( $product_specifications ) );
		
		// serialize product specificatons
		foreach ( $ps_array as $ps ) {
			if ( '' != $ps ) {
				list( $spec_name, $spec_value, $sequence ) = explode( '`', $ps );
				$product_specs[] = array( $spec_name, $spec_value, $sequence );
			}
		}

		if ( empty( $list_price ) || _('List Price (Optional)') == $list_price )
			$list_price = 0;
		
		$this->db->update( 'products', array(
				'brand_id' => $brand_id,
				'industry_id' => $industry_id,
				'name' => $name,
				'slug' => $slug,
				'description' => $description,
				'status' => $status,
				'sku' => $sku,
				'price' => $price,
				'list_price' => $list_price,
				'weight' => $weight,
				'volume' => $volume,
				'product_specifications' => serialize( $product_specs ),
				'publish_visibility' => $publish_visibility,
				'publish_date' => $publish_date,
				'user_id_modified' => $user['user_id'],
			), array( 'product_id' => $product_id, 'website_id' => $user['website']['website_id'] ), 'iisssssdddisssi', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add Products
	 *
	 * @param int $product_id
	 * @param array $categories_array an array of category ids to add
	 * @param object $c Categories class
	 * @return
	 */
	public function add_product( $product_id, $categories_array, $c ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$this->db->update( 'website_products', array( 'active' => 1 ), array( 'product_id' => $product_id, 'website_id' => $website_id ), 'i', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the website's categories and compare to the categories_array
		$website_categories = $this->db->get_col( "SELECT `category_id` FROM `website_categories` WHERE `website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix should not have to loop
		foreach ( $categories_array as $cid ) {
			// If it's not in the website categories, add it
			$parent_categories = $c->get_parent_category_ids( $cid );
			
			// Make sure we have all the parents
			if ( is_array( $parent_categories ) )
			foreach ( $parent_categories as $pcid ) {
				if ( !in_array( $pcid, $website_categories ) )
					$this->add_product_category( $product_id, $pcid );
			}
			
			// Make sure we have the categories themselves
			if ( !in_array( $cid, $website_categories ) )
				$this->add_product_category( $product_id, $cid );
		}
		
		return true;
	}
	
	/**
	 * Adds a product category
	 *
	 * @param int $product_id
	 * @param int $category_id
	 * @return bool
	 */
	private function add_product_category( $product_id, $category_id ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// Insert a new website category -- need to get an image
		$image = $this->db->get_var( "SELECT `image` FROM `product_images` WHERE `product_id` = $product_id AND `sequence` = 0 LIMIT 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product image.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Instantiate new class
		$p = new Products;
		
		// Create image url
		$image_url = 'http://' . $p->get_industry( $product_id ) . '.retailcatalog.us/products/' . $product_id . '/' . $image;
		
		$this->db->insert( 'website_categories', array( 'website_id' => $website_id, 'category_id' => $category_id, 'image_url' => $image_url ), 'iis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add website category.', __LINE__, __METHOD__ );
			return false;
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
		
		foreach ( $images as $image ) {
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
	 * Changes a product industry
	 *
	 * @param int $product_id
	 * @param int $industry_id
	 * @return bool
	 */
	public function change_industry( $product_id, $industry_id ) {
		global $user;
		
		$this->db->update( 'products', array( 'industry_id' => $industry_id ), array( 'product_id' => $product_id, 'website_id' => $user['website']['website_id'] ), 'i', 'ii' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to change custom product industry.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Removes website products
	 *
	 * @param int $product_id
	 * @param object Categories class
	 * @return tru
	 */
	public function remove_product( $product_id, $c ) {
		global $user;
		
		// Type Juggling
		$product_id = (int) $product_id;
		$website_id = (int) $user['website']['website_id'];
		
		$website_results = $this->db->get_results( "SELECT `website_id` FROM `website_products` WHERE `product_id` = $product_id AND `website_id` = $website_id AND `active` = 1", ARRAY_A );
		$website_results = $website_results[0];
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website results.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( !$website_results )
			return true;
		
		// Set all the products as inactive
		$this->db->update( 'website_products', array( 'active' => 0 ), array( 'product_id' => $product_id, 'website_id' => $website_id ), 'i', 'ii' );
			
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website product.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the product categories
		$category_ids = $this->get_product_categories( $product_id );
		
		if ( !$category_ids )
			return false;
		
		$category_ids_string = '';
		
		foreach ( $category_ids as $cid ) {
			$parent_categories = $c->get_parent_category_ids( $cid );
			
			// Delete parent categories if the website doesn't have any products
			foreach ( $parent_categories as $pc_id ) {
				// @Fix there should be a more efficient way than looping this query
				if ( !$this->without_products( $pc_id, $c ) )
					continue;
				
				// Add this to the list of categories that needs to be deleted
				if ( !empty( $category_ids_string ) )
					$category_ids_string .= ',';
				
				$category_ids_string .= $pc_id;
			}
			
			// See if this category and all ones under this
			if ( !$this->without_products( $cid, $c ) )
				continue;
			
			// Add this to the list of categories that needs to be deleted
			if ( !empty( $category_ids_string ) )
				$category_ids_string .= ',';
			
			$category_ids_string .= $cid;
		}
		
		if ( !empty( $category_ids_string ) ) {
			$this->db->query( "DELETE FROM `website_categories` WHERE `website_id` = $website_id AND `category_id` IN($category_ids_string)" );
				
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to deleted parent website categories.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}

    /**
     * Remove Discontinued Products
     *
     * @return bool
     */
    public function remove_discontinued_products() {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        $this->db->query( "UPDATE `website_products` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) SET a.`active` = 0 WHERE a.`website_id` = $website_id AND a.`active` = 1 AND b.`status` = 'discontinued'" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get delete product images.', __LINE__, __METHOD__ );
			return false;
		}

        // Reorganize the categories
        $this->reorganize_categories();

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
	 * Adds product categories
	 *
	 * @param int $product_id
	 * @param array $categories
	 * @return bool
	 */
	public function add_categories( $product_id, $categories ) {
		// Type Juggling
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
	 * Check to see if a category is without products without products
	 *
	 * @param int $category_id
	 * @param array
	 * @return array
	 */
	private function without_products( $category_id, $c ) {
		global $user;
		
		// Type Juggling
		$website_id = $user['website']['website_id'];
		
		$categories = $c->get_sub_category_ids( $category_id );
		$categories[] = $category_id;
		
		// @Fix shouldn't need to do the count
		$count = $this->db->get_var( "SELECT COUNT(*) FROM `website_products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`active` = 1 AND a.`website_id` = $website_id AND b.`category_id` IN(" . implode( ',', $categories ) . ')', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get websites without products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( $count > 0 ) ? true : false;
	}
	
	/**
	 * Updates wesbite product sequence
	 *
	 * @param array $sequence
	 * @return bool
	 */
	 public function update_website_products_sequence( $sequence ) {
		global $user;
		 
		// Type Juggle
		$website_id = (int) $user['website']['website_id'];
		 
		// Prepare statement
		$statement = $this->db->prepare( "UPDATE `website_products` SET `sequence` = ? WHERE `product_id` = ? AND `website_id` = $website_id" );
		$statement->bind_param( 'ii', $count, $product_id );
		
		foreach ( $sequence as $count => $product_id ) {
			$statement->execute();
			
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( 'Failed to update website product sequence', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Removes all sale items from a website
	 *
	 * @return bool
	 */
	public function remove_sale_items() {
		global $user;
		
		$this->db->update( 'website_products', array( 'on_sale' => 0 ), array( 'website_id' => $user['website']['website_id'] ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to remove all sale items.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
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
	 * Gets the data for an autocomplete request for all products
	 *
	 * @param string $query
	 * @param string $field
	 * @return bool
	 */
	public function autocomplete( $query, $field ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// @Fix do we need to support the non deprecated method as well?
		// Deprecated, but needed for old files that are still like this
		//$query = htmlentities( stripslashes( trim( $query ) ), ENT_QUOTES, 'UTF-8' );
		
		// Support more than one field
		if ( is_array( $field ) ) {
			$where = ' AND ';
			
			// The initial and last paren are needed due to the multiple static-WHERE's
			foreach ( $field as $f ) {
				$where .= ( empty( $where ) ) ? ' AND ( ' : ' OR ';
				
				$where .= 'a.`' . $this->db->escape( $f ) . "` LIKE '%" . $this->db->escape( $query ) . "%'";
			}
			
			// Close the open paren
			$where .= ' )';
			
			// Escape the primary field
			$field = $this->db->escape( $field[0] );
		} else {
			$where = " AND a.`" . $this->db->escape( $field ) . "` LIKE '%" . $this->db->escape( $query ) . "%'";
		}
		
		$suggestions = $this->db->get_results( "SELECT a.`product_id` AS value, a.`$field` AS name FROM `products` AS a LEFT JOIN `website_industries` AS b ON ( a.`industry_id` = b.`industry_id` ) WHERE a.`publish_visibility` = 'public' AND ( a.`website_id` = 0 OR a.`website_id` = $website_id  ) AND b.`website_id` = $website_id $where ORDER BY a.`$field` LIMIT 10", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocompleted products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Gets the data for an autocomplete request for owned products
	 *
	 * @param string $query
	 * @param string $field
	 * @return bool
	 */
	public function autocomplete_owned( $query, $field ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		// @Fix do we need to support the non deprecated method as well?
		// Deprecated, but needed for old files that are still like this
		//$query = htmlentities( stripslashes( trim( $query ) ), ENT_QUOTES, 'UTF-8' );
		
		// Support more than one field
		if ( is_array( $field ) ) {
			$where = '';
			
			// The initial and last paren are needed due to the multiple static-WHERE's
			foreach ( $field as $f ) {
				$where .= ( empty( $where ) ) ? ' AND ( ' : ' OR ';
				
				$where .= '`' . $this->db->escape( $f ) . "` LIKE '%" . $this->db->escape( $query ) . "%'";
			}
			
			// Close the open paren
			$where .= ' )';
			
			// Escape the primary field
			$field = $this->db->escape( $field[0] );
		} else {
			$where = " AND `" . $this->db->escape( $field ) . "` LIKE '%" . $this->db->escape( $query ) . "%'";
		}
		
        $suggestions = $this->db->get_results( "SELECT DISTINCT b.`product_id` AS value, b.`$field` AS name FROM `website_products` AS a INNER JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `website_industries` as c ON ( b.`industry_id` = c.`industry_id` ) WHERE b.`publish_visibility` = 'public' AND a.`website_id` = $website_id AND a.`active` = 1 AND a.`website_id` = $website_id $where ORDER BY `$field` LIMIT 10", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocompleted items on owned products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * Gets the data for an autocomplete request for custom products
	 *
	 * @param string $query
	 * @param string $field
	 * @return bool
	 */
	public function autocomplete_custom( $query, $field ) {
		global $user;
		
		// Type Juggling
		$website_id = (int) $user['website']['website_id'];
		
		$where = " AND `" . $this->db->escape( $field ) . "` LIKE '%" . $this->db->escape( $query ) . "%'";
		
		$suggestions = $this->db->get_results( "SELECT DISTINCT b.`product_id` AS value, b.`$field` AS name FROM `website_products` AS a INNER JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `website_industries` as c ON ( b.`industry_id` = c.`industry_id` ) WHERE b.`publish_visibility` = 'public' AND a.`website_id` = $website_id AND a.`active` = 1 AND c.`website_id` = $website_id $where ORDER BY `$field` LIMIT 10", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocompleted items on custom products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $suggestions;
	}
	
	/**
	 * List website products
	 *
	 * @param array $variables( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_website_products( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$website_products = $this->db->get_results( "SELECT a.`product_id`, a.`name`, a.`sku`, a.`status`, b.`name` AS brand FROM `products` AS a LEFT JOIN `brands` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN website_products AS c ON ( a.`product_id` = c.`product_id` ) WHERE c.`active` = 1 $where GROUP BY a.`product_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list website products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_products;
	}
	
	/**
	 * Count the website products
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_website_products( $where ) {
		$count = $this->db->get_col( "SELECT a.`product_id` FROM `products` AS a LEFT JOIN `brands` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN website_products AS c ON ( a.`product_id` = c.`product_id` ) WHERE c.`active` = 1 $where GROUP BY a.`product_id`" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count website products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix -- shouldn't have to use PHP's count
		return count( $count );
	}
	
		
	/**
	 * List custom products
	 *
	 * @param array $variables( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_custom_products( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$products = $this->db->get_results( "SELECT a.`product_id`, a.`name`, d.`name` AS brand, a.`sku`, a.`status`, DATE( a.`publish_date` ) AS publish_date, c.`name` AS category FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON ( b.category_id = c.category_id ) LEFT JOIN `brands` AS d ON ( a.brand_id = d.brand_id ) WHERE a.`publish_visibility` <> 'deleted' $where GROUP BY a.`product_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get custom products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Counts products
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_custom_products( $where ) {
		$count = $this->db->get_col( "SELECT a.`product_id` FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON ( b.category_id = c.category_id ) LEFT JOIN `brands` AS d ON ( a.brand_id = d.brand_id ) WHERE a.`publish_visibility` <> 'deleted' $where GROUP BY a.`product_id`" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count custom products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix shouldn't have to use count
		return count( $count );
	}
	
	/**
	 * List custom products
	 *
	 * @param array $variables( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_add_products( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$products = $this->db->get_results( "SELECT a.`product_id`, a.`name`, a.`sku`, a.`status`, b.`name` AS brand FROM `products` AS a LEFT JOIN `brands` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN `product_categories` AS c ON ( a.`product_id` = c.`product_id` ) WHERE 1 $where GROUP BY a.`product_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list add products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Counts products
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_add_products( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( DISTINCT( a.`product_id` ) ) FROM `products` AS a LEFT JOIN `brands` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN `product_categories` AS c ON ( a.`product_id` = c.`product_id` ) WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count add products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix should not require the count function
		return $count;
	}

    /**
	 * List product prices
	 *
	 * @param array $variables( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_product_prices( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$products = $this->db->get_results( "SELECT a.`product_id`, a.`alternate_price`, a.`price`, a.`sale_price`, a.`alternate_price_name`, a.`price_note`, b.`sku` FROM `website_products` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`active` = 1 AND b.`publish_visibility` = 'public' AND b.`publish_date` <> '0000-00-00 00:00:00' $where GROUP BY a.`product_id` $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list products prices.', __LINE__, __METHOD__ );
			return false;
		}
        
		return $products;
	}

	/**
	 * Count the product prices
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_product_prices( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( a.`product_id` ) FROM `website_products` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`active` = 1 AND b.`publish_visibility` = 'public' AND b.`publish_date` <> '0000-00-00 00:00:00' $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count product prices.', __LINE__, __METHOD__ );
			return false;
		}

		return $count;
	}

    /**
     * Set Product Prices
     *
     * @param array $values
     * @return bool
     */
    public function set_product_prices( $values ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

         // Prepare statement
		$statement = $this->db->prepare( "UPDATE `website_products` SET `alternate_price` = ?, `price` = ?, `sale_price` = ?, `alternate_price_name` = ?, `price_note` = ? WHERE `website_id` = $website_id AND `active` = 1 AND `product_id` = ?" );
		$statement->bind_param( 'dddssi', $alternate_price, $price, $sale_price, $alternate_price_name, $price_note, $product_id );

		foreach ( $values as $product_id => $array ) {
			// Make sure all values have a value
			$alternate_price = 0;
			$price = 0;
			$sale_price = 0;
			$alternate_price_name = '';
			$price_note = '';
		
			// Get the values
            extract( $array );
			
			
            $statement->execute();

			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( 'Failed to update website products sequence', __LINE__, __METHOD__ );
				return false;
			}
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
		$website_id = (int) $user['website']['website_id'];
		
		// Make sure it's a real product
		$exists = $this->db->get_var( "SELECT `product_id` FROM `products` WHERE `product_id` = $product_id AND `website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check if product exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Check to see if it exists
		if ( !$exists )
			return false;
		
		// Clone product
		$this->db->query( "INSERT INTO `products` ( `website_id`, `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, `user_id_created`, `date_created` ) SELECT $website_id, `brand_id`, `industry_id`, CONCAT( `name`, ' (Clone)' ), CONCAT( `slug`, '-2' ), `description`, `status`, CONCAT( `sku`, '-2' ), `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, $user_id, NOW() FROM `products` WHERE `product_id` = $product_id AND `website_id` = $website_id" );
		
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
	 * Dump Brand
	 *
	 * @param int $brand_id
	 * @return bool
	 */
	public function dump_brand( $brand_id ) {
		global $user;
		
		// Instantiate class
		$w = new Websites;
		
		// Get industries
		$industries = preg_replace( '/[^0-9,]/', '', implode( ',', $w->get_website_industries() ) );
		if ( $industries == '' ) {
			return array( false, 0, true );
		}
		
		// Type Juggling
		$brand_id = (int) $brand_id;
		$website_id = (int) $user['website']['website_id'];
		
		// Magical Query #1
		// Get the count of the products that would be added (exclude ones that the website already has)
		$brand_product_count = $this->db->get_var( "SELECT COUNT( a.`product_id` ) FROM `products` AS a LEFT JOIN `brands` AS b ON ( a.`brand_id` = b.`brand_id` ) LEFT JOIN `website_products` AS c ON ( a.`product_id` = c.`product_id` AND c.`website_id` = $website_id ) WHERE ( a.`website_id` = 0 OR a.`website_id` = $website_id ) AND a.`industry_id` IN ( $industries ) AND a.`publish_visibility` = 'public' AND b.`brand_id` = $brand_id AND ( c.`product_id` IS NULL OR c.`active` = 0 )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product count.', __LINE__, __METHOD__ );
			return false;
		}
		
		// How many free slots do we have
		$free_slots = $user['website']['products'] - $this->get_website_products_count();
		
		// Get the quantity
		$quantity = $free_slots - $brand_product_count;
		
		// If we don't have the space
		if ( $quantity < 0 )
			return array( false, $quantity * -1 );
		
		// How many products are we adding?
		$quantity = $brand_product_count;
		
		// Magical Query #2
		// Insert website products
		$this->db->query( "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $website_id, a.`product_id` FROM `products` AS a LEFT JOIN `website_products` AS b ON ( a.`product_id` = b.`product_id` AND b.`website_id` = $website_id ) WHERE ( a.`website_id` = 0 OR a.`website_id` = $website_id ) AND a.`industry_id` IN($industries) AND a.`publish_visibility` = 'public' AND a.`status` <> 'discontinued' AND a.`brand_id` = $brand_id AND ( b.`product_id` IS NULL OR b.`active` = 0 ) ON DUPLICATE KEY UPDATE `active` = 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to dump website products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get category IDs
		$category_ids = $this->db->get_col( "SELECT DISTINCT a.`category_id` FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `website_categories` AS c ON ( a.`category_id` = c.`category_id` AND c.`website_id` = $website_id ) WHERE ( b.`website_id` = 0 OR b.`website_id` = $website_id ) AND b.`industry_id` IN($industries) AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND b.`brand_id` = $brand_id AND c.`category_id` IS NULL" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product categories.', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there are any categories that need to be added
		if ( !empty( $category_ids ) ) {
			// Need to get the parent categories
			$c = new Categories;
				
			$parent_category_ids = $used_parent_category_ids = array();
			
			foreach ( $category_ids as $cid ) {
				$parent_category_ids[$cid] = $c->get_parent_category_ids( $cid );
			}
			
			$category_images = $this->db->get_results( "SELECT a.`category_id`, CONCAT( 'http://', c.`name`, '.retailcatalog.us/products/', b.`product_id`, '/', d.`image` ) FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `industries` AS c ON ( b.`industry_id` = c.`industry_id` ) LEFT JOIN `product_images` AS d ON ( b.`product_id` = d.`product_id` ) LEFT JOIN `website_categories` AS e ON ( a.`category_id` = e.`category_id` AND e.`website_id` = $website_id) WHERE a.`category_id` IN(" . implode( ',', $category_ids ) . ") AND ( b.`website_id` = 0 OR b.`website_id` = $website_id ) AND b.`brand_id` = $brand_id AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND d.`sequence` = 0 AND e.`category_id` IS NULL GROUP BY a.`category_id`", ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get website category images.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Create insert
			$values = '';
			$category_images = ar::assign_key( $category_images, 'category_id', true );
			
			foreach ( $category_ids as $cid ) {
				if ( !empty( $values ) )
					$values .= ',';
				
				// This image will be used for the parent categories as well
				$image = $this->db->escape( $category_images[$cid] );
				$values .= "( $website_id, $cid, '$image' )";
				
				foreach ( $parent_category_ids[$cid] as $pcid ) {
					// Don't set the same parent category twice
					if ( in_array( $pcid, $used_parent_category_ids ) )
						continue;
					
					if ( !empty( $values ) )
						$values .= ',';
					
					$values .= "( $website_id, $pcid, '$image' )";
					
					// Add it to the list
					$used_parent_category_ids[] = $pcid;
				}
			}
			
			// Add the values
			if ( !empty( $values ) ) {
				$this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES $values ON DUPLICATE KEY UPDATE `category_id` = VALUES( `category_id` )" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to add website categories.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}
		
		return array( true, $quantity, false );
	}

    /**
	 * Add Bulk
	 *
	 * @param string $product_skus
	 * @return bool
	 */
	public function add_bulk( $product_skus ) {
		global $user;

        // Turn the SKUs into an array
        $product_skus = explode( "\n", $product_skus );

        // Make sure they entered in SKUs
        if ( !is_array( $product_skus ) || empty( $product_skus ) )
            return false;

        // Escape all the SKUs
        foreach ( $product_skus as &$ps ) {
            $ps = "'" . $this->db->escape( trim( $ps ) ) . "'";
        }

        // Turn it into a string
        $product_skus = implode( ",", $product_skus );
        
		// Instantiate class
		$w = new Websites;

		// Get industries
		$industries = preg_replace( '/[^0-9,]/', '', implode( ',', $w->get_website_industries() ) );

		if ( $industries == '' )
			return array( false, 0, true );

		// Type Juggling
		$website_id = (int) $user['website']['website_id'];

		// Magical Query #1
		// Get the count of the products that would be added (exclude ones that the website already has)
		$product_count = $this->db->get_var( "SELECT COUNT( a.`product_id` ) FROM `products` AS a LEFT JOIN `website_products` AS b ON ( a.`product_id` = b.`product_id` AND b.`website_id` = $website_id ) WHERE a.`industry_id` IN ( $industries ) AND a.`publish_visibility` = 'public' AND a.`sku` IN ( $product_skus ) AND ( b.`product_id` IS NULL OR b.`active` = 0 )" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product count.', __LINE__, __METHOD__ );
			return false;
		}

		// How many free slots do we have
		$free_slots = $user['website']['products'] - $this->get_website_products_count();

		// Get the quantity
		$quantity = $free_slots - $product_count;

		// If we don't have the space
		if ( $quantity < 0 )
			return array( false, $quantity * -1 );

		// How many products are we adding?
		$quantity = $product_count;

		// Magical Query #2
		// Insert website products
		$this->db->query( "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $website_id, a.`product_id` FROM `products` AS a LEFT JOIN `website_products` AS b ON ( a.`product_id` = b.`product_id` AND b.`website_id` = $website_id ) WHERE a.`industry_id` IN($industries) AND a.`publish_visibility` = 'public' AND a.`status` <> 'discontinued' AND a.`sku` IN ( $product_skus ) AND ( b.`product_id` IS NULL OR b.`active` = 0 ) ON DUPLICATE KEY UPDATE `active` = 1" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to dump website products.', __LINE__, __METHOD__ );
			return false;
		}

		// Get category IDs
		$category_ids = $this->db->get_col( "SELECT DISTINCT a.`category_id` FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `website_categories` AS c ON ( a.`category_id` = c.`category_id` AND c.`website_id` = $website_id ) WHERE b.`industry_id` IN($industries) AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND b.`sku` IN ( $product_skus ) AND c.`category_id` IS NULL" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product categories.', __LINE__, __METHOD__ );
			return false;
		}

		// If there are any categories that need to be added
		if ( !empty( $category_ids ) ) {
			// Need to get the parent categories
			$c = new Categories;

			$parent_category_ids = $used_parent_category_ids = array();

			foreach ( $category_ids as $cid ) {
				$parent_category_ids[$cid] = $c->get_parent_category_ids( $cid );
			}

			$category_images = $this->db->get_results( "SELECT a.`category_id`, CONCAT( 'http://', c.`name`, '.retailcatalog.us/products/', b.`product_id`, '/', d.`image` ) FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `industries` AS c ON ( b.`industry_id` = c.`industry_id` ) LEFT JOIN `product_images` AS d ON ( b.`product_id` = d.`product_id` ) LEFT JOIN `website_categories` AS e ON ( a.`category_id` = e.`category_id` AND e.`website_id` = $website_id ) WHERE a.`category_id` IN(" . implode( ',', $category_ids ) . ") AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND b.`sku` IN ( $product_skus ) AND d.`sequence` = 0 AND e.`category_id` IS NULL GROUP BY a.`category_id`", ARRAY_A );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get website category images.', __LINE__, __METHOD__ );
				return false;
			}

			// Create insert
			$values = '';
			$category_images = ar::assign_key( $category_images, 'category_id', true );

			foreach ( $category_ids as $cid ) {
				if ( !empty( $values ) )
					$values .= ',';

				// This image will be used for the parent categories as well
				$image = $this->db->escape( $category_images[$cid] );
				$values .= "( $website_id, $cid, '$image' )";

				foreach ( $parent_category_ids[$cid] as $pcid ) {
					// Don't set the same parent category twice
					if ( in_array( $pcid, $used_parent_category_ids ) )
						continue;

					if ( !empty( $values ) )
						$values .= ',';

					$values .= "( $website_id, $pcid, '$image' )";

					// Add it to the list
					$used_parent_category_ids[] = $pcid;
				}
			}

			// Add the values
			if ( !empty( $values ) ) {
				$this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES $values ON DUPLICATE KEY UPDATE `category_id` = VALUES( `category_id` )" );

				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to add website categories.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}

		return array( true, $quantity, false );
	}
	
	/**
	 * Sets a product as inactive
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function delete( $product_id ) {
		global $user;
		
		$this->db->update( 'products', array( 'publish_visibility' => 'deleted' ), array( 'product_id' => $product_id, 'website_id' => $user['website']['website_id'] ), 's', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete product.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get website coupon ids
	 *
	 * @return array
	 */
	private function website_coupons() {
		global $user;
		
		$website_coupons = $this->db->get_col( 'SELECT `website_coupon_id` FROM `website_coupons` WHERE `website_id` = ' . (int) $user['website']['website_id'] );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website coupon ids.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_coupons;
	}

    /**
	 * Reorganize Categories
	 *
	 * @return array( int, int ) removed categories, new categories
	 */
	public function reorganize_categories() {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		// Get category IDs
		$category_ids = $this->db->get_col( "SELECT DISTINCT b.`category_id` FROM `website_products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`website_id` = $website_id AND a.`active` = 1" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product categories.', __LINE__, __METHOD__ );
			return false;
		}

		// IF NULL exists, remove it
		if ( $key = array_search( NULL, $category_ids ) )
			unset( $category_ids[$key] );

		// Get website category IDs
		$website_category_ids = $this->db->get_col( "SELECT DISTINCT `category_id` FROM `website_categories` WHERE `website_id` = $website_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website product categories.', __LINE__, __METHOD__ );
			return false;
		}

		// IF NULL exists, remove it
		if ( $key = array_search( NULL, $website_category_ids ) )
			unset( $website_category_ids[$key] );

		// Need to get the parent categories
		$c = new Categories;

		$new_category_ids = $product_category_ids = $remove_category_ids = array();

		// Find out what categories we need to add
		if ( is_array( $category_ids ) )
		foreach ( $category_ids as $cid ) {
			if ( empty( $cid ) )
				continue;

			// Start forming complete list of product categories
			$product_category_ids[] = $cid;

			// If the website does not already have the category and it has not already been added
			if ( !in_array( $cid, $website_category_ids ) && !in_array( $cid, $new_category_ids ) )
				$new_category_ids[] = $cid;

			// Get the parent categories of this category
			$parent_category_ids = $c->get_parent_category_ids( $cid );

			// Loop through parent ids
			if ( is_array( $parent_category_ids ) )
			foreach ( $parent_category_ids as $pcid ) {
				// Forming complete list
				$product_category_ids[] = $pcid;

				// If the website does not already have it and it has not already been added
				if ( !in_array( $pcid, $website_category_ids ) && !in_array( $pcid, $new_category_ids ) )
					$new_category_ids[] = $pcid;
			}
		}

		// Only want the unique values
		$product_category_ids = array_unique( $product_category_ids );

		// IF NULL exists, remove it
		if ( $key = array_search( NULL, $product_category_ids ) )
			unset( $product_category_ids[$key] );

		sort( $product_category_ids );

		foreach ( $website_category_ids as $wcid ) {
			if ( !in_array( $wcid, $product_category_ids ) )
				$remove_category_ids[] = $wcid;
		}

		// Bulk add categories
		$this->bulk_add_categories( $new_category_ids, $c );

		// Remove extra categoryes
		$this->remove_categories( $remove_category_ids );

        return array( count( $remove_category_ids ), count( $new_category_ids ) );
	}

	/**
	 * Bulk Add categories
	 *
	 * @param array $category_ids
	 * @param object $c (Category)
	 * @return bool
	 */
	private function bulk_add_categories( $category_ids, $c ) {
        if ( !is_array( $category_ids ) || 0 == count( $category_ids ) )
			return;

        global $user;

		// Type Juggling
		$website_id = (int) $user['website']['website_id'];

		// If there are any categories that need to be added
		$category_images = $this->db->get_results( "SELECT a.`category_id`, CONCAT( 'http://', c.`name`, '.retailcatalog.us/products/', b.`product_id`, '/', d.`image` ) FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `industries` AS c ON ( b.`industry_id` = c.`industry_id` ) LEFT JOIN `product_images` AS d ON ( b.`product_id` = d.`product_id` ) LEFT JOIN `website_products` AS e ON ( b.`product_id` = e.`product_id` ) WHERE a.`category_id` IN(" . implode( ',', $category_ids ) . ") AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND d.`sequence` = 0 AND e.`website_id` = $website_id AND e.`product_id` IS NOT NULL GROUP BY a.`category_id`", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website category images.', __LINE__, __METHOD__ );
			return false;
		}

		// Create insert
		$values = '';
		$category_images = ar::assign_key( $category_images, 'category_id', true );

		foreach ( $category_ids as $cid ) {
			// If we have an image, use it
			if ( isset( $category_images[$cid] ) ) {
				$image = $this->db->escape( $category_images[$cid] );
			} else {
				// If not, that means it is a parent category. Choose the first child category with an image, and use it

				// Get child categories
				$child_categories = $c->get_child_categories( $cid );

				// Find the first available image
				foreach ( $child_categories as $cc ) {
					if ( isset( $category_images[$cc['category_id']] ) ) {
						// Assign the image
						$image = $this->db->escape( $category_images[$cc['category_id']] );

						// Don't need to loop any furhter
						break;
					}
				}
			}

			// Create the CSV
			if ( !empty( $values ) )
				$values .= ',';

			// Create the values
			$values .= "( $website_id, $cid, '$image' )";
		}

		// Add the values
		if ( !empty( $values ) ) {
			$this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES $values ON DUPLICATE KEY UPDATE `category_id` = VALUES( `category_id` )" );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to add website categories.', __LINE__, __METHOD__ );
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove Categories from a website
	 *
	 * @param array $category_ids
	 * @return bool
	 */
	private function remove_categories( $category_ids ) {
        global $user;

		// Type Juggling
		$website_id = (int) $user['website']['website_id'];

		// Make sure we're dealing with an array
		if ( !is_array( $category_ids ) || 0 == count( $category_ids ) )
			return true;

		// Make sure they're MySQL safe
		foreach ( $category_ids as &$cid ) {
			$cid = (int) $cid;
		}

		$this->db->query( "DELETE FROM `website_categories` WHERE `website_id` = $website_id AND `category_id` IN(" . implode( ',', $category_ids ) . ')' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website categories.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
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
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}