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
	 * @param array $variables( $where, $order_by, $limit )
	 * @return array $craigslist_ads
	 */
	public function get_craigslist_ads( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$craigslist_ads = $this->db->get_results( "SELECT a.`title`, a.`craigslist_ad_id`, a.`text`, c.`name` AS `product_name`, c.`sku`, a.`date_created`, a.`date_posted`
												 FROM `craigslist_ads` AS a LEFT JOIN `products` AS c ON( a.product_id = c.product_id )
												 WHERE a.`active` = 1 $where GROUP BY a.`craigslist_ad_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist_ads;
	}

   	/**
	 * Countscraigslist ads
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_craigslist_ads( $where ) {
		$craiglist_ad_count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`craigslist_ad_id` ) FROM `craigslist_ads` AS a LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) WHERE a.`active` = 1 $where GROUP BY a.`craigslist_ad_id`" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}

		return $craiglist_ad_count;
	}
	
	/**
	 * Gets a single ad
	 *
	 * @param int $craigslist_ad_id
	 * @return array
	 */
	public function get( $craigslist_ad_id ) {
		$results = $this->db->prepare( "SELECT a.`craigslist_ad_id`, a.`title`, a.`text`, a.`price`, a.`product_id`, b.`title` AS store_name, c.`name` AS product_name,
												 c.`sku`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, UNIX_TIMESTAMP( a.`date_posted` ) AS date_posted
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
	 * Gets a random headline
	 *
	 * @param int $category_id
	 * @return string
	 */
	public function get_random_headline( $category_id ) {
        // Type Juggling
        $category_id = (int) $category_id;

		$headlines = $this->db->get_col( "SELECT `headline` FROM `craigslist_headlines` WHERE `category_id` = $category_id" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist headline.', __LINE__, __METHOD__ );
			return false;
		}

        // Get a random headline
        $headline = $headlines[rand( 0, count( $headlines ) - 1 )];

		return ( isset( $headline ) ) ? $headline : '';
	}
	
	/**
	 * Creates a new Craigslist ad
	 *
	 * @param int $product_id
	 * @param string $title
	 * @param string $text
     * @param float $price
	 * @param bool $post
	 * @return int craigslist_ad_id
	 */
	public function create( $product_id, $title, $text, $price, $post ) {
        global $user;

        // Determine if we're publishing
        if ( $post ) {
            $date = new DateTime();
            $date_posted = $date->format('Y-m-d H:i:s');
        } else {
            $date_posted = '0000-00-00 00:00:00';
        }

       $this->db->insert( 'craigslist_ads', array(
            'website_id' => $user['website']['website_id']
            , 'product_id' => $product_id
            , 'title' => $title
            , 'text' => $text
            , 'price' => $price
            , 'date_posted' => $date_posted
            , 'date_created' => dt::date( "Y-m-d H:i:s" )
        ), 'iissdss' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to create Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->db->insert_id;
	}

    /**
	 * Updates an existing Craigslist ad
	 *
     * @param int $craigslist_ad_id
	 * @param int $product_id
	 * @param string $title
	 * @param string $text
     * @param float $price
	 * @param bool $post
	 * @return bool
	 */
	public function update( $craigslist_ad_id, $product_id, $title, $text, $price, $post ) {
		global $user;

        // Determine if we're publishing
        if ( $post ) {
            $date = new DateTime();
            $date_posted = $date->format('Y-m-d H:i:s');
        } else {
            $date_posted = '0000-00-00 00:00:00';
        }

		$result = $this->db->update( 'craigslist_ads', array(
            'product_id' => $product_id
            , 'title' => $title
            , 'text' => $text
            , 'price' => $price
            , 'date_posted' => $date_posted
        ), array( 'craigslist_ad_id' => $craigslist_ad_id, 'website_id' => $user['website']['website_id'] ), 'issdis', 'ii' );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}
	
	/**
	 * Deletes a craigslist ad from the database
	 *
	 * @param int $craigslist_ad_id
	 * @return bool
	 */
	public function delete( $craigslist_ad_id ) {
        global $user;

		$this->db->update( 'craigslist_ads', array( 'active' => '0' ), array( 'craigslist_ad_id' => $craigslist_ad_id, 'website_id' => $user['website']['website_id'] ), 'i', 'ii' );
		
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
	 * @var int $craigslist_ad_id
	 * @return bool false if couldn't delete
	 */
	public function copy( $craigslist_ad_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $craigslist_ad_id = (int) $craigslist_ad_id;

        $this->db->query( "INSERT INTO `craigslist_ads` ( `website_id`, `product_id`, `title`, `text`, `price`, `date_created` ) SELECT `website_id`, `product_id`, `title`, `text`, `price`, NOW() FROM `craigslist_ads` WHERE `craigslist_ad_id` = $craigslist_ad_id AND `website_id` = $website_id" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to copy Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}
		return true;
	}

    /**
     * Download Craigslist
     *
     * @return array
     */
    public function download() {
        global $user;

        // Type Juggling
        $website_id = $user['website']['website_id'];

        $craigslist_ads = $this->db->get_results( "SELECT a.`title`, a.`text`, b.`description`, b.`name`,b.`sku`, c.`category_id`, d.`name` AS category, e.`name` AS brand, CONCAT( 'http://', g.`name`, '.retailcatalog.us/products/', b.`product_id`, '/', f.`image` ) AS image FROM `craigslist_ads` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `product_categories` AS c ON ( a.`product_id` = c.`product_id`) LEFT JOIN `categories` AS d ON ( c.`category_id` = d.`category_id` ) LEFT JOIN `brands` AS e ON ( b.`brand_id` = e.`brand_id` ) LEFT JOIN `product_images` AS f ON ( b.`product_id` = f.`product_id` ) LEFT JOIN `industries` AS g ON ( b.`industry_id` = g.`industry_id` ) WHERE a.`website_id` = $website_id AND a.`active` = 1 AND a.`product_id` <> 0 AND f.`sequence` = 0", ARRAY_A );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get Craigslist Ads.', __LINE__, __METHOD__ );
			return false;
		}

        $c = new Categories();

        foreach( $craigslist_ads as &$cad ) {
            $category = $c->get_top( $cad['category_id'] );
            $cad['top_category'] = $category['name'];
        }

        return $craigslist_ads;
    }

    /**
     * Get Craigslist Market
     *
     * @param int $craigslist_market_id
     * @return array
     */
    public function get_craigslist_market( $craigslist_market_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $craigslist_market_id = (int) $craigslist_market_id;

        $market = $this->db->get_row( "SELECT a.`craigslist_market_id`, CONCAT( a.`city`, ', ', IF( '' <> a.`area`, CONCAT( a.`state`, ' - ', a.`area` ), a.`state` ) ) AS market FROM `craigslist_markets` AS a LEFT JOIN `craigslist_market_links` AS b ON ( a.`craigslist_market_id` = b.`craigslist_market_id` ) WHERE a.`craigslist_market_id` = $craigslist_market_id AND b.`website_id` = $website_id", ARRAY_A );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get Craigslist Market.', __LINE__, __METHOD__ );
			return false;
		}

        return $market;
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
