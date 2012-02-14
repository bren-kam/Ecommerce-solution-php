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
	 * Get Craigslist Template
	 *
	 * Gets a specific craigslist template
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get( $craigslist_template_id ) {
		$craigslist = $this->db->get_row( 'SELECT a.`craigslist_template_id`, a.`title`, a.`description`, a.`category_id`, b.`name` AS `category_name` FROM `craigslist_templates` AS a LEFT JOIN `categories` AS b ON (a.`category_id` = b.`category_id`) WHERE a.`craigslist_template_id` = ' . (int) $craigslist_template_id, ARRAY_A );
	
		// Handle any error
		if ( mysql_errno() ) {
			$this->err( 'Failed to get craigslist template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist;
	}
	
	
	/**
	 * Creates a new Craigslist ad
	 * @param int $category_id
	 * @param string $title
	 * @param string $description
	 * @return int craigslist_template_id
	 */
	public function create( $category_id, $title, $description ) { 
		$this->db->insert( 'craigslist_templates',
						  array( 'category_id' => $category_id, 'title' => $title, 'description' => $description, 'publish_visibility' => 'visible', 'date_created' => dt::date('Y-m-d H:i:s') ),
						  'issss' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create craigslist ad template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Updates a Craigslist ad template
	 *
	 * @param int $craigslist_template_id
	 * @param int $category_id
	 * @param string $title
	 * @param string $description
	 * @return int craigslist_template_id
	 */
	public function update( $craigslist_template_id, $category_id, $title, $description ) {
		
		$this->db->update( 'craigslist_templates', 
						  array( 'category_id' => $category_id, 'title' => $title, 'description' => $description, 'publish_visibility' => 'visible' ),
						  array( 'craigslist_template_id' => $craigslist_template_id ),
						  'isss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update craigslist template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get all information of the craigslist templates
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_craigslist( $where, $order_by, $limit ) {
		$where .= '' . ( ( isset( $where ) ) ? $where . " AND a.`publish_visibility` = 'visible' " : " WHERE a.`publish_visibility` = 'visible'" );
								 
		// Get the templates
		$craigslist_templates = $this->db->get_results( "SELECT a.`craigslist_template_id`, a.`title`, a.`description`, b.`name` AS `category_name`, a.`category_id`, a.`date_created`
														FROM `craigslist_templates` as a INNER JOIN `categories` as b ON ( a.`category_id` = b.`category_id` )
														$where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist templates.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist_templates;
	}
	
	/**
	 * Sets a craigslist template as inactive
	 *
	 * @param int $craigslist_template_id
	 * @return bool
	 */
	public function delete( $craigslist_template_id ) {
		$this->db->update( 'craigslist_templates', array( 'publish_visibility' => 'deleted' ), array( 'craigslist_template_id' => $craigslist_template_id), 's', 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete craigslist template.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Count all the craigslist templates
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_craigslist( $where ) {		
		if ( isset( $where ) && $where ) {
			$where .= " AND a.`publish_visibility` = 'visible' ";
		} else {
			$where = " WHERE a.`publish_visibility` = 'visible' ";
		}
		
		// Get the craigslist template count
		$craigslist_count = count( $this->db->get_results( "SELECT COUNT( a.`craigslist_template_id` ) FROM `craigslist_templates` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) {$where} GROUP BY a.`craigslist_template_id`", ARRAY_A ) );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist templates.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist_count;
    }

	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
	 * @return bool
	 */
	public function autocomplete( $query, $field ) {
		$sql = "SELECT DISTINCT( $field ) FROM `craigslist_templates` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) WHERE $field LIKE '%$query%' AND a.`publish_visibility` = 'visible' ORDER BY $field";
		
		// Get results
		$results = $this->db->get_results( $sql, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocomplete entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}

	/**
	 * Gets random data for creating a preview
	 * 
	 * @param int $category_id
     * @param bool $bottom
	 * @return array $results
	 */
	public function get_preview_data( $category_id, $bottom ) {
	/*	[Product Name]
		[Store Name]
		[Category]
		[Brand]
		[Product Description]
		[Product Specs]
		[Photo]
		[Attributes]
		[SKU]					*/
		
		$results = $this->db->get_row( "SELECT 
									  a.`product_id`, 
									  d.`category_id`,
									  a.`name` AS `product_name`,
									  d.`name` AS `category`,
									  c.`name` AS `brand`,
									  a.`description` AS `product_description`,
									  a.`product_specifications` AS `product_specs`,
									  a.`sku`
									FROM `products` AS a 
									LEFT JOIN `product_categories` AS b ON (a.`product_id` = b.`product_id`) 
									LEFT JOIN `brands` AS c ON (a.`brand_id` = c.`brand_id` )
									LEFT JOIN `categories` AS d ON ( d.`category_id` = b.`category_id`)
									WHERE b.`category_id` = " . (int) $category_id . " AND ( a.`product_id` > " . (int) $bottom . " ) LIMIT 1", ARRAY_A );
		
		$attributes = $this->db->get_results( "SELECT
											 b.`attribute_item_name`,
											 c.`name` AS `attribute_name`
											 FROM `attribute_item_relations` AS a
											 LEFT JOIN `attribute_items` AS b ON (b.`attribute_item_id` = a.`attribute_item_id`)
											 LEFT JOIN `attributes` AS c ON (c.`attribute_id` = b.`attribute_id`)
											 WHERE a.`product_id` = " . (int) $results['product_id'] . " ORDER BY b.`sequence`", ARRAY_A );
		
		$photos = $this->db->get_results( "SELECT a.`image`, c.`name` AS `industry`, a.`sequence`, a.`product_id`
											 FROM `product_images` AS a 
											 LEFT JOIN `products` AS b ON (b.`product_id` = a.`product_id`)
											 LEFT JOIN `industries` AS c ON (b.`industry_id` = c.`industry_id`)
											 WHERE a.`product_id` = " . (int) $results['product_id'] . " ORDER BY a.`sequence`", ARRAY_A );
		
		$results['photos'] = $photos;
		$results['attributes'] = $attributes;
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get preview data.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}

    /***** HEADLINES *****/

    /**
     * Get Craigslist Headline
     *
     * @param int $craigslist_headline_id
     * @return array
     */
    public function get_headline( $craigslist_headline_id ) {
        // Type Juggling
        $craigslist_headline_id = (int) $craigslist_headline_id;

        $headline = $this->db->get_row( "SELECT `craigslist_headline_id`, `category_id`, `headline` FROM `craigslist_headlines` WHERE `craigslist_headline_id` = $craigslist_headline_id", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get headline.', __LINE__, __METHOD__ );
			return false;
		}

        return $headline;
    }

    /**
     * Create Craigslist Headline
     *
     * @param int $category_id
     * @param string $headline
     * @return int
     */
    public function create_headline( $category_id, $headline ) {
        $this->db->insert( 'craigslist_headlines', array( 'category_id' => $category_id, 'headline' => $headline, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create headline.', __LINE__, __METHOD__ );
			return false;
		}

        return $this->db->insert_id;
    }

    /**
     * Delete a Craigslist Headline
     *
     * @param int $craigslist_headline_id
     * @return bool
     */
    public function delete_headline( $craigslist_headline_id ) {
        // Type Juggling
        $craigslist_headline_id = (int) $craigslist_headline_id;

        // Delete the market
        $this->db->query( "DELETE FROM `craigslist_headlines` WHERE `craigslist_headline_id` = $craigslist_headline_id" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete craigslist headline.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
     * Update Craigslist Headlines
     *
     * @param int $craigslist_headline_id
     * @param int $category_id
     * @param string $headline
     * @return bool
     */
    public function update_headline( $craigslist_headline_id, $category_id, $headline ) {
        $this->db->update( 'craigslist_headlines', array( 'category_id' => $category_id, 'headline' => $headline ), array( 'craigslist_headline_id' => $craigslist_headline_id ), 'is', 'i' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update headline.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
	 * List Craigslist Headlines
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_craigslist_headlines( $where, $order_by, $limit ) {
		// Get the headlines
		$headlines = $this->db->get_results( "SELECT a.`craigslist_headline_id`, a.`headline`, a.`date_created`, b.`name` AS category FROM `craigslist_headlines` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) WHERE 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list craigslist headlines.', __LINE__, __METHOD__ );
			return false;
		}

		return $headlines;
	}

    /**
	 * Count the craigslist headlines
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_craigslist_headlines( $where ) {
		// Get the craigslist market count
		$headline_count = $this->db->get_var( "SELECT COUNT( a.`craigslist_headline_id` ) FROM `craigslist_headlines` AS a LEFT JOIN `categories` AS b ON ( a.`category_id` = b.`category_id` ) WHERE 1 $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist headlines.', __LINE__, __METHOD__ );
			return false;
		}

		return $headline_count;
	}

    /***** MARKETS *****/

    /**
     * Get Craigslist Market
     *
     * @param int $craigslist_market_id
     * @return array
     */
    public function get_market( $craigslist_market_id ) {
        // Type Juggling
        $craigslist_market_id = (int) $craigslist_market_id;

        $market = $this->db->get_row( "SELECT `craigslist_market_id`, `state`, `city`, `area` FROM `craigslist_markets` WHERE `craigslist_market_id` = $craigslist_market_id", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get market.', __LINE__, __METHOD__ );
			return false;
		}

        return $market;
    }

    /**
     * Create Craigslist Market
     *
     * @param string $state
     * @param string $city
     * @param string $area
     * @return int
     */
    public function create_market( $state, $city, $area ) {
        $this->db->insert( 'craigslist_markets', array( 'state' => $state, 'city' => $city, 'area' => $area, 'date_created' => dt::date('Y-m-d H:i:s') ), 'ssss' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create market.', __LINE__, __METHOD__ );
			return false;
		}

        return $this->db->insert_id;
    }

    /**
     * Update Craigslist Market
     *
     * @param int $craigslist_market_id
     * @param string $state
     * @param string $city
     * @param string $area
     * @return bool
     */
    public function update_market( $craigslist_market_id, $state, $city, $area ) {
        $this->db->update( 'craigslist_markets', array( 'state' => $state, 'city' => $city, 'area' => $area ), array( 'craigslist_market_id' => $craigslist_market_id ), 'sss', 'i' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update market.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
     * Delete a Craigslist Market
     *
     * @param int $craigslist_market_id
     * @return bool
     */
    public function delete_market( $craigslist_market_id ) {
        // Type Juggling
        $craigslist_market_id = (int) $craigslist_market_id;

        // Delete the market
        $this->db->query( "DELETE FROM `craigslist_markets` WHERE `craigslist_market_id` = $craigslist_market_id" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete craigslist market.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
	 * List Craigslist Markets
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_craigslist_markets( $where, $order_by, $limit ) {
		// Get the markets
		$markets = $this->db->get_results( "SELECT `craigslist_market_id`, CONCAT( IF( '' <> `area`, CONCAT( `area`, ' - ', `city` ), `city` ), ', ', `state` )	AS market, `date_created` FROM `craigslist_markets` WHERE 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list craigslist markets.', __LINE__, __METHOD__ );
			return false;
		}

		return $markets;
	}

    /**
	 * Count the craigslist markets
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_craigslist_markets( $where ) {
		// Get the craigslist market count
		$market_count = $this->db->get_var( "SELECT COUNT(`craigslist_market_id`) FROM `craigslist_markets` WHERE 1 $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist markets.', __LINE__, __METHOD__ );
			return false;
		}

		return $market_count;
	}

    /***** OTHER *****/

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