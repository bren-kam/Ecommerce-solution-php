<?php
/**
 * Handles all the stuff for Products
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
	 * Get Tab
	 *
	 * @param string $fb_page_id
	 * @return string
	 */
	public function get_tab( $fb_page_id ) {
		// Get the tab
		$tab_data = $this->db->prepare( "SELECT IF( 0 = b.`product_catalog`, a.`content`, 'no-catalog' ) AS content, a.`website_id`, IF( '' = b.`subdomain`, b.`domain`, CONCAT( b.`subdomain`, '.', b.`domain` ) ) AS domain FROM `sm_products` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`fb_page_id` = ?", 's', $fb_page_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get tab.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( 'no-catalog' == $tab_data['content'] ) {
			// Initial variables
			$product_ids = array();
			$tab = '';
			
			// Get top categories
			$categories = $this->db->get_results( 'SELECT a.`name`, a.`slug`, b.`image_url`, COALESCE( c.`width`, 0 ) AS width, COALESCE( c.`height`, 0 ) AS height FROM `categories` AS a LEFT JOIN `website_categories` AS b ON ( a.`category_id` = b.`category_id` ) LEFT JOIN `website_image_dimensions` AS c ON ( b.`website_id` = c.`website_id` AND ( b.`image_url` = c.`image_url` OR c.`image_url` IS NULL ) ) WHERE a.`parent_category_id` = 0 AND b.`website_id` = ' . (int) $tab_data['website_id'], ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get top categories.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Declare variables for loop
			$i = 1;
			$total_categories = count( $categories );
			
			// Create rows
			foreach ( $categories as $cat ) {
				if ( 1 == $i % 3 ) {
					$last_class = ( $total_categories - $i < 3 ) ? ' last' : '';
					$tab .= "<ul class='clear$last_class'>";
					$open = true;
				}
				
				// @Fix keep this for later
				$padding = ( 0 == $i % 3 ) ? '' : ' class="padding"';
				
				$image_width = $cat['width'];
				$image_height = $cat['height'];
				
				// If the category didn't have it, get the image url and store it for the future
				if ( 0 == $image_width || 0 == $image_height ) {
					list( $image_width, $image_height ) = getimagesize( $cat['image_url'] );
					
					// Store it in the database
					$this->db->insert( 'website_image_dimensions', array( 'website_id' => $tab_data['website_id'], 'image_url' => $cat['image_url'], 'width' => $image_width, 'height' => $image_height, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isiis' );
				
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to insert website image dimensions.', __LINE__, __METHOD__ );
						return false;
					}
				}
				
				// Adjust images
				if ( empty( $image_width ) )
					$image_width = 1;
				
				list( $image_width, $image_height ) = image::proportions( $image_width, $image_height, 160, 160 );
				$category_padding = ( 160 - $image_height ) / 2;
				
				// Create tab listing
				$tab .= '<li' . $padding . '><a href="http://' . $tab_data['domain'] . '/' . $cat['slug'] . '/" class="img" title="' . $cat['name'] . '" target="_blank"><img src="' . $cat['image_url'] . '" width="' . $image_width . '" height="' . $image_height . '" alt="' . $cat['name'] . '" style="padding:' . $category_padding . 'px 0" /></a><br /><a href="http://' . $tab_data['domain'] . '/' . $cat['slug'] . '/" title="' . $cat['name'] . '" target="_blank">' . $cat['name'] . "</a></li>\n";
				
				if ( 0 == $i % 3 ) {
					$tab .= "</ul>\n";
					$open = false;
				}
				
				$i++;
			}
			
			if ( $open )
				$tab .= "</ul>\n";
		} else {
			$tab = $tab_data['content'];
		}

		return $tab;
	}
	
	/**
	 * Connect a website
	 *
	 * @param string $fb_page_id
	 * @param string $key
	 * @return array
	 */
	public function connect( $fb_page_id, $key ) {
		// Connect the websites
		$this->db->update( 'sm_products', array( 'fb_page_id' => $fb_page_id ), array( 'key' => $key ), 's', 's' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Connected Website
	 *
	 * @param int $fb_user_id
	 * @return array
	 */
	public function get_connected_website( $fb_page_id ) {
		// Type Juggling
		$fb_page_id = (int) $fb_page_id;
		
		// Get the connected website
		$website = $this->db->get_row( "SELECT a.`title`, b.`key` FROM `websites` AS a LEFT JOIN `sm_products` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`fb_page_id` = $fb_page_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
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