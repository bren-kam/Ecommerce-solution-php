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
		if( !parent::__construct() )
			return false;
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
		if( $this->db->errno() ) {
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
		if( $this->db->errno() ) {
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
		$results = $this->db->prepare( "SELECT COUNT( DISTINCT `craigslist_template_id`) FROM `craigslist_templates` WHERE `category_id` = ? AND `publish_visibility` = 'visible'", 'i', $category_id )->get_var( '' );
		
		// Handle any error
		if( $this->db->errno() ) {
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
	 * @param int $template_index
	 * @return string
	 */
	public function get_template( $category_id, $direction, $template_index ) {
		// Type Juggling
		$category_id = (int) $category_id;
		$direction = (int) $direction;
		$template_index = (int) $template_index;
		
		$limit = ' LIMIT ' . ( $template_index - 1 ) . ', 1';
		/*
		switch ( $direction ) {
			default:
			case 1:
				$where = " a.`craigslist_template_id` > $template_id";
				$order = ' ASC';
			break;
			
			case -1:
				$where = " a.`craigslist_template_id` < $template_id";
				$order = ' DESC';
			break;
			
			
				$where = " a.`craigslist_template_id` = $start";
				$order = ' ASC';
			break;
		}
		*/
		
		$template = $this->db->get_row( "SELECT a.`craigslist_template_id`, a.`title`, a.`description`, a.`category_id`, b.`name` AS `category_name` FROM `craigslist_templates` AS a LEFT JOIN `categories` AS b ON (a.`category_id` = b.`category_id`) WHERE a.`category_id` = $category_id AND a.`publish_visibility` = 'visible' ORDER BY a.`craigslist_template_id` ASC" . $limit, ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $template;
	}
	
	/**
	 * Countscraigslist ads
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_craigslist_ads( $where ) {
		// @Fix need to make this count without PHP's count
		$craigslist_ad_ids = $this->db->get_results( "SELECT a.`craigslist_ad_id` FROM `craigslist_ads` AS a LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) WHERE a.`active` = '1' $where GROUP BY a.`craigslist_ad_id`", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return count( $craigslist_ad_ids );
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
		if( $this->db->errno() ) {
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
		if( $this->db->errno() ) {
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
		if( $this->db->errno() ) {
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
		if( $this->db->errno() ) {
			$this->err( 'Failed to update Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return $result;
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
