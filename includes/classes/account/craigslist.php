<?php
/**
 * Handles all the craiglist functions
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Craigslist extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query the query that was given
	 * @param string $field the field that is needed
	 * @param int $website_id the id of the website being searched for
	 * @return array
	 */
	public function autocomplete( $query, $field, $website_id ) {
		$results = $this->db->get_results( "SELECT DISTINCT a.`$field` FROM `products` AS a LEFT JOIN `website_industries` as b ON ( a.`industry_id` = b.`industry_id` ) LEFT JOIN `website_products` AS c ON ( a.`product_id` = c.`product_id` ) WHERE ( a.`website_id` = 0 || a.`website_id` = $website_id ) AND a.`publish_visibility` = 'public' AND b.`website_id` = $website_id AND c.`website_id` = $website_id AND `$field` LIKE '$query%' ORDER BY `$field`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to perform autocomplete', __LINE__, __METHOD__ );
			return false;
		}
				
		return $results;
	}
	
	/**
	 * Gets craigslist ads
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array $craigslist_ads
	 */
	public function get_craigslist_ads( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$craigslist_ads = $this->db->get_results( "SELECT a.`title`, a.`craigslist_ad_id`, a.`text`, a.`duration`, 
												 c.`name` AS `product_name`, c.`sku`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, UNIX_TIMESTAMP( a.`date_posted` ) AS date_posted 
												 FROM `craigslist_ads` AS a 
												 LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) 
												 WHERE a.`active` = '1' $where GROUP BY a.`craigslist_ad_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist_ads;
	}
	
	/**
	 * Gets a single ad
	 *
	 * @param int $craigslist_ad_id
	 * @return array
	 */
	public function get( $craigslist_ad_id ) {
		$results = $this->db->prepare( "SELECT a.`title`, a.`craigslist_ad_id`, a.`text`, a.`duration`, a.`product_id`,
									  			 b.`title` AS store_name,
												 c.`name` AS product_name, 
												 c.`sku`, 
												 UNIX_TIMESTAMP( a.`date_created` ) AS date_created, UNIX_TIMESTAMP( a.`date_posted` ) AS date_posted 
												 FROM `craigslist_ads` AS a 
												 LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) 
												 LEFT JOIN `products` AS c ON ( a.product_id = c.product_id ) 
												 WHERE a.`craigslist_ad_id` = ? LIMIT 1", 'i', $craigslist_ad_id )->get_row('', ARRAY_A);
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Count the number of templates for a particular category
	 *
	 * @param int $category_id
	 * @return int number of ads.
	 */
	public function count_templates_for_category( $category_id ){
		$results = $this->db->prepare( "SELECT COUNT(`craigslist_template_id`) FROM `craigslist_templates` WHERE `category_id` = ? AND `publish_visibility` = 'visible'", 'i', $category_id )->get_var( '' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist templates.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Gets a single craigslist ad templates
	 *
	 * @param int $category_id
	 * @param int $direction
	 * @param int $start
	 * @param string $order
	 * @return template
	 */
	public function get_template( $category_id, $direction, $start, $order){		
		$start = intval( $start );
		
		switch ( $direction ){
			case 1:
				$where = " a.`craigslist_template_id` > $start ";
				$order = " ASC";
				break;
			
			case -1:
				$where = " a.`craigslist_template_id` < $start ";
				$order = " DESC";
				break;
				
			default:
				$where = " a.`craigslist_template_id` = $start ";
				$order = " ASC";
				break;
		}
	  
		
		$results = $this->db->prepare( "
						 SELECT a.`craigslist_template_id`, a.`title`, a.`description`, a.`category_id`, b.`name` AS `category_name` 
						 FROM `craigslist_templates` AS a LEFT JOIN `categories` AS b ON (a.`category_id` = b.`category_id`) 
						 WHERE ( " . $where . " ) AND a.`category_id` = ? ORDER BY a.`craigslist_template_id` " . $order . " LIMIT 1
						 "
						, 'i' , $category_id )->get_row('', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Countscraigslist ads
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_craigslist_ads( $where ) {
		// @Fix need to make this count without PHP's count
		$craigslist_ad_ids = $this->db->get_results( "SELECT a.`craigslist_ad_id`
												 FROM `craigslist_ads` AS a 
												 LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) 
												 WHERE a.`active` = '1' $where GROUP BY a.`craigslist_ad_id`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return count( $craigslist_ad_ids );
	}
	
	/**
	 * Gets the product_id searched by a criterion
	 *
	 * @param string $search_by
	 * @param string $query
	 * @return int product id
	 */
	public function get_product_id( $search_by, $query )
	{
		if ( !$search_by || !$query) return false;
		
		switch ( $search_by ) {
			case 'sku':
				$search_by = 'sku';
				break;
			case 'product_name':
				$search_by = 'name';
			break;
			default:
				return false;
			break;
		}

		$result = $this->db->prepare( "SELECT `product_id` FROM `products` WHERE `$search_by` = ?", 's', $query )->get_var( '' );
		//echo "|$result|";
		//exit;
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $result;
	}
		
	/**
	 * Gets a single product of product_id.
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product( $product_id ) {
		// Type Juggling
		$product_id = (int) $product_id;
		
		$product = $this->db->get_row( "SELECT a.`description`, d.`name` as `brand`, a.`product_id`, a.`name` AS `product_name`, c.`category_id`, c.`name` AS `category_name`, a.`sku`, a.`product_specifications` FROM `products` AS a INNER JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `categories` AS c ON ( b.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS d ON ( a.`brand_id` = d.`brand_id` ) WHERE ( a.`product_id` = $product_id ) LIMIT 1", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product info.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $product;
	}
	
	/**
	 * Retrieves the partial URLs of all images for a given Product_id
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product_image_urls( $product_id ) {
		$results = $this->db->get_col( "SELECT CONCAT( 'http://', b.`name`, '.retailcatalog.us/products/', c.`product_id`, '/', a.`image` ) AS image_url FROM `product_images` AS a LEFT JOIN `products` AS c ON (a.`product_id` = c.`product_id`) LEFT JOIN `industries` AS b ON (c.`industry_id` = b.`industry_id`) WHERE a.`product_id` = " . (int)$product_id . " ORDER BY a.`sequence` ASC LIMIT 10" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get product image urls.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Creates a new Craigslist ad
	 *
	 * @param int $craigslist_template_id
	 * @param int $product_id
	 * @param int $website_id
	 * @param int $duration
	 * @param string $title
	 * @param string $description
	 * @param int $active
	 * @param bool $publish
	 * @return int craigslist_ad_id
	 */
	public function create( $craigslist_template_id, $product_id, $website_id, $duration, $title, $text, $active, $publish ){ 
		// echo $craigslist_template_id, ' : ', $product_id, ' : ', $website_id, ' : ', $duration, ' : ', $title, ' : ', $text, ' : ', $active, ' : ', $publish, ' : '; exit;
		$date = ( $publish ) ? date( "Y-m=d H:i:s", time() ) : "0";
		$result = $this->db->insert( 'craigslist_ads', 
						  array( 'craigslist_template_id' => $craigslist_template_id, 
								 'product_id' => $product_id,
								 'website_id' => $website_id,
								 'duration' => $duration,
								 'title' => $title,
								 'text' => $text,
								 'active' => $active,
								 'date_created' => date( "Y-m=d H:i:s", time() ),
								 'date_posted' => $date ),
						  'iiiississ' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return $result;
	}
	
	/**
	 * Deletes a craigslist ad from the database
	 *
	 * @param int $craigslist_ad_id
	 * @return bool
	 */
	public function delete( $craigslist_ad_id ) {			
		$this->db->update( 'craigslist_ads', array( 'active' => '0' ), array( 'craigslist_ad_id' => $craigslist_ad_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return true;
	}
	
	/**
	 * Clones a craigslist ad from the database
	 *
	 * @since 1.0.0
	 *
	 * @var int $craigslist_ad_id
	 * @return bool false if couldn't delete
	 */
	public function copy( $craigslist_ad_id ) {
		$ad = $this->db->prepare( "SELECT `craigslist_template_id`, `product_id`, `website_id`, `title`, `text`, `craigslist_city_id`, `craigslist_category_id`, `craigslist_district_id` FROM `craigslist_ads` WHERE `craigslist_ad_id` = ?", 'i', $craigslist_ad_id )->get_row('', ARRAY_A);
		$this->db->insert( 'craigslist_ads', array( 'craigslist_template_id' => $ad['craigslist_template_id'], 'product_id' => $ad['product_id'], 'website_id' => $ad['website_id'], 'title' => $ad['title'], 'text' => $ad['text'], 'craigslist_city_id' => $ad['craigslist_city_id'], 'craigslist_category_id' => $ad['craigslist_category_id'], 'craigslist_district_id' => $ad['craigslist_district_id'], 'date_created' => date( "Y-m=d H:i:s", time() ) ), 'iiissiiis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to copy Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return true;
	}
	
	/**
	 * Updates an existing Craigslist ad
	 *
	 * @param int $craigslist_ad_id
	 * @param int $craigslist_template_id
	 * @param int $product_id
	 * @param int $website_id
	 * @param int $duration
	 * @param string $title
	 * @param string $description
	 * @param int $active
	 * @param bool $publish
	 * @return int craigslist_ad_id
	 */
	public function update( $craigslist_ad_id, $craigslist_template_id, $product_id, $website_id, $duration, $title, $text, $active, $publish ){		
		$date = ( $publish ) ? date( "Y-m=d H:i:s", time() ) : "0";
		$result = $this->db->update( 'craigslist_ads', 
						  array( 'craigslist_template_id' => $craigslist_template_id, 
								 'product_id' => $product_id,
								 'website_id' => $website_id,
								 'duration' => $duration,
								 'title' => $title,
								 'text' => $text,
								 'active' => $active,
								 'date_updated' => date( "Y-m=d H:i:s", time() ),
								 'date_posted' => $date ),
			array( 'craigslist_ad_id' => $craigslist_ad_id ), 'iiiississ', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return $result;
	}
	
	/**
	 * Gets misc website info
	 *
	 * @param int $websites_id
	 * @return array
	 */
	public function get_website_info( $website_id ){
		$results = $this->db->prepare( "SELECT `title`, `domain`, `logo` FROM `websites` WHERE `website_id` = ?", 'i', $website_id )->get_row('', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website info.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
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