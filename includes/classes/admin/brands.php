<?php
/**
 * Handles all the brands
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Brands extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}

    /**
     * Create a brand simply
     *
     * @param string $name
     * @return int
     */
    public function create_simple( $name ) {
        // Create the brand
		$this->db->insert( 'brands', array( 'name' => $name, 'slug' => format::slug( $name ) ), 'ss' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create brand.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->db->insert_id;
    }
	/**
	 * Creates a brand and puts it into the database
	 *
	 * @param string $name the brand name
	 * @param string $slug
	 * @param string $link the link to the brand's website
	 * @param file $image the product image file
	 * @param string $product_options
	 * @return int
	 */
	public function create( $name, $slug, $link, $image, $product_options ) {
		// Get rid of the slashes and another other characters in the slug
		$image_slug = format::slug( $name );
		
		// Get the extension
		$image_extension = strtolower( f::extension( $image['name'] ) );
		
		// Don't insert a picture if one wasn't uploaded
		$image_link = ( !empty( $image['name'] ) ) ? 'http://brands.retailcatalog.us/' . $image_slug . '.' . $image_extension : '';
		
		// Create the brand
		$this->db->insert( 'brands', array( 'name' => $name, 'slug' => $slug, 'link' => $link, 'image' => $image_link ), 'ssss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		$brand_id = $this->db->insert_id;
		
		if ( !empty( $image['name'] ) ) {
			$f = new Files();
			$f->upload_image( $image, $image_slug, 120, 120, 'brands', '', true, false );
		}
		
		// Add product options if there are any
		$product_options = explode( '|', $product_options );
		
		if ( is_array( $product_options ) ) {
			$values = '';
			
			foreach ( $product_options as $po ) {
				// Make sure we don't have a blank
				if ( empty( $po ) )
					continue;
				
				// Add commas between each value
				if ( !empty( $values ) )
					$values .= ',';
				
				// Typecast
				$po = (int) $po;
				
				// Create values
				$values .= "( $po, $brand_id )";
			}
			
			if ( !empty( $values ) ) {
				$this->db->query( "INSERT INTO `product_option_relations` ( `product_option_id`, `brand_id` ) VALUES $values" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert product option relations.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}
		
		return $brand_id;
	}
	
	/**
	 * Creates a brand and puts it into the database
	 *
	 * @param int $brand_id
	 * @param string $name the brand name
	 * @param string $slug
	 * @param string $link the link to the brand's website
	 * @param file $image the product image file
	 * @param string $product_options
	 * @return bool
	 */
	public function update( $brand_id, $name, $slug, $link, $image, $product_options ) {		
		// Typecast
		$brand_id = (int) $brand_id;
		
		// Get the old brand
		$old_brand = $this->get( $brand_id );
		
		// If they don't enter in a new image, then use the old one
		if ( !empty( $image['name'] ) ) {
			// Get rid of the slashes and another other characters in the slug
			$image_slug = format::slug( $name );
	
			// Get the extension
			$image_extension = strtolower( f::extension( $image['name'] ) );
			
			// Don't insert a picture if one wasn't uploaded
			$image_link = ( !empty( $image['name'] ) ) ? 'http://brands.retailcatalog.us/' . $image_slug . '.' . $image_extension : '';
		} else {
			// Assign to the old image
			$image_link = $old_brand['image'];
		}
		
		// Create the brand
		$this->db->update( 'brands', array( 'name' => $name, 'slug' => $slug, 'link' => $link, 'image' => $image_link ), array( 'brand_id' => $brand_id ), 'ssss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( !empty( $image['name'] ) ) {
			$f = new Files();
			
			// Delete old image
			$old_url_info = parse_url( $old_brand['image'] );
			$old_image_path = substr( $old_url_info['path'], 1 );
			
			if ( !empty( $old_image_path ) )
				$f->delete_image( $old_image_path, 'brands' );
			
			return $f->upload_image( $image, $image_slug, 120, 120, 'brands', '', true, true );
		}
				
		// Delete product option relations
		$this->db->query( "DELETE FROM `product_option_relations` WHERE `brand_id` = $brand_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete product option relations.', __LINE__, __METHOD__ );
			return false;
		}
				
		// Add product options if there are any		
		if ( "" != $product_options ) {
			$product_options = explode( '|', $product_options );
		}
		
		if ( is_array( $product_options ) ) {
			$values = '';
			
			foreach ( $product_options as $po ) {
				// Make sure we don't have a blank
				if ( empty( $po ) )
					continue;
				
				// Add commas between each value
				if ( !empty( $values ) )
					$values .= ',';
				
				// Typecast
				$po = (int) $po;
				
				// Create values
				$values .= "( $po, $brand_id )";
			}
						
			$this->db->query( "INSERT INTO `product_option_relations` ( `product_option_id`, `brand_id` ) VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to insert product option relations.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Get All brands and return in an associative array
	 *
	 * @return array
	 */
	public function get_all() {
		$brands = $this->db->get_results( 'SELECT * FROM `brands` ORDER BY `name` ASC', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brands;
	}
	
	/**
	 * Get all information of the brands
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_brands( $where, $order_by, $limit ) {
		// Get the brands
		$brands = $this->db->get_results( "SELECT `brand_id`, `name`, `link` FROM `brands` WHERE 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brands;
	}
	
	/**
	 * Count all the brands
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_brands( $where ) {
		// Get the brand count
		$brand_count = $this->db->get_var( "SELECT COUNT( `brand_id` ) FROM `brands` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count brands.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $brand_count;
	}
	
	/**
	 * Gets a specific brand
	 *
	 * @param int $brand_id
	 * @param bool $get_product_options (optional|true)
	 * @return array
	 */
	public function get( $brand_id, $get_product_options = true ) {
		// Typecast
		$brand_id = (int) $brand_id;
		
		// Get the brand
		$brand = $this->db->get_row( 'SELECT * FROM `brands` WHERE `brand_id` = ' . $brand_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( $get_product_options ) {
			$brand['product_options'] = $this->db->get_col( "SELECT `product_option_id` FROM `product_option_relations` WHERE `brand_id` = $brand_id" );
		
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get product options.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return $brand;
	}
	
	/**
	 * Gets the data for an autocomplete
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete( $query ) {
		global $user;
		
		// Get results
		$results = $this->db->prepare( "SELECT `brand_id` AS object_id, `name` AS brand FROM `brands` WHERE `name` LIKE ? ORDER BY `name`", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get autocomplete entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Delete a brand
	 *
	 * Deletes a brand and removes the image from the bucket (if there is an image)
	 *
 	 * @param int $brand_id
	 * @return array
	 */
	public function delete( $brand_id ) {
		$po = new Product_Options;
		$brand_id = (int) $brand_id;
		$brand = $this->get( $brand_id, false );
		
		$this->db->query( "DELETE FROM `brands` WHERE `brand_id` = $brand_id LIMIT 1" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete brand.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete product options by brand
		if ( !$po->delete_by_brand( $brand_id ) )
			return false;
		
		if ( !empty( $brand['image'] ) ) {
			$f = new Files();
			
			$url_info = parse_url( $brand['image'] );
			$image_path = substr( $url_info['path'], 1 );
			
			if ( !empty( $image_path ) )
				$f->delete_image( $image_path, 'brands' );
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
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}