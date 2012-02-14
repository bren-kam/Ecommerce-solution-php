<?php

/**
 * Handles all analytics information
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Analytics extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Gets a set of analytics data within a number of dates
	 *
	 * @param int $ga_profile_id
	 * @return array
	 */
	 public function get_date_pages( $ga_profile_id ) {
		$ga_profile_id = (int) $ga_profile_id;
		
		$rows = $this->db->get_results( "SELECT `page`, REPLACE( `date`, '-', '' ) AS date FROM `analytics_data` WHERE `ga_profile_id` = $ga_profile_id AND `date` >= '2010-06-13' AND `date` <= '2010-07-13'", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get analytics data.', __LINE__, __METHOD__ );
			return false;
		}
		
		$pages = $dates = array();
		
		if ( is_array( $rows ) )
		foreach ( $rows as $r ) {
			$pages[] = $r['page'];
			$dates[] = $r['date'];
		}
		
		return array( $pages, $dates );
	 }
	 
	/**
	 * Adds a new record to the analytics data
	 *
	 * @param int $ga_profile_id
	 * @param string $page (Dimension) The page path (i.e. /products/)
	 * @param string $source (Dimension) The referring source (i.e. google.com)
	 * @param string $medium (Dimension) What type of referrer is it ( direct, referral, organic )
	 * @param string $keyword (Dimension) they keyword they used to access this page
	 * @param int $bounces (Metric) The amount of single page visits
	 * @param int $entrances (Metric) The amount of people who entered the website from this page
	 * @param int $exits (Metric) The amount of people who exited the website from this page
	 * @param int $new_visits (Metric) The amount of new visits
	 * @param int $page_views (Metric) The amount of views (not unique) for this page
	 * @param int $time_on_page (Metric) the amount of time (in seconds) on a page
	 * @param int $visits (Metric) The total amount of visits
	 * @param string $date (Dimension) The date it was created
	 * @return bool
	 */
	 public function add( $ga_profile_id, $page, $source, $medium, $keyword, $bounces, $entrances, $exits, $new_visits, $page_views, $time_on_page, $visits, $date ) {
		//$this->db->query( sprintf( "INSER
		//`analytics_data` ( `ga_profile_id`, `page`, `source`, `medium`, `keyword`, `bounces`, `entrances`, `exits`, `new_visits`, `page_views`, `time_on_page`, `visits`, `date` ) VALUES ( %d, '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, %d, %d, '%s' )",
		//$ga_profile_id, format::sql_string( $page ), format::sql_string( $source ), format::sql_string( $medium ), mysql_real_escape_string( format::sql_string( $keyword ) ), $bounces, $entrances, $exits, $new_visits, $page_views, $time_on_page, $visits, $date ) );

		// $this->db->insert( 'products', array( 'user_id_created' => $user_id, 'date_created' => dt::date('Y-m-d H:i:s') ), 'is' );

		$this->db->insert( 'analytics_data', array( 'ga_profile_id' => $ga_profile_id, 'page' => $page, 'source' => $source, 'medium' => $medium, 'keyword' => $keyword, 'bounces' => $bounces, 'entrances' => $entrances, 'exits' => $exits, 'new_visits' => $new_visits, 'page_views' => $page_views, 'time_on_page' => $time_on_page, 'visits' => $visits, 'date' => $date ), 'issssiiiiiiis' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add analytics data.', __LINE__, __METHOD__ );
			return false;
		}

	 	return true;
	 }

    /**
	 * Adds new records to craigslist ads
	 *
     * @param string $date
	 * @return bool
	 */
	public function add_craigslist_stats( $date ) {
        $craigslist_website_ids = $this->db->get_results( "SELECT `website_id`, `value` FROM `website_settings` WHERE `key` = 'craigslist-customer-id'", ARRAY_A );

         // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to get craigslist website ids.', __LINE__, __METHOD__ );
            return false;
        }

         // Get the customer > website id link
        $craigslist_website_ids = ar::assign_key( $craigslist_website_ids, 'value', true );

         // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist = new Craigslist_API( 2, 'uCYS6jfM6XbyBfZJ' );

        // Get the stats
        $stats = $craigslist->get_stats( $date );

        // Initialize variables
        $values = $tag_ids = array();

        foreach ( $stats as $s ) {
            $date = $this->db->escape( $s->date );
            $website_id = (int) $craigslist_website_ids[$s->customer_id];

            // Add Marketing
            $values[] = "( $website_id, " . (int) $s->market_id . ", 'market', " . (int) $s->overall->unique . ', ' . (int) $s->overall->views . ', ' . $s->overall->posts . ", '" . $date . "' )";

            if ( is_array( $s->tags ) )
            foreach ( $s->tags as $t ) {
                $tag_ids[] = $t->tag_id;

                // Add Marketing
                $values[] = "( $website_id, " . (int) $t->tag_id . ", 'tag', " . (int) $t->unique . ', ' . (int) $t->views . ', ' . $t->posts . ", '" . $date . "' )";
            }
        }

        // Add at up to 500 at a time
        $value_chunks = array_chunk( $values, 500 );

        foreach ( $value_chunks as $vc ) {
            $this->db->query( "INSERT INTO `analytics_craigslist` VALUES " . implode( ',', $vc ) );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->err( 'Failed to add craigslist value chunks.', __LINE__, __METHOD__ );
                return false;
            }
        }


        // Get tags
        $tags = $craigslist->get_tags( $tag_ids );

        $values = array();

        if ( is_array( $tags ) )
        foreach ( $tags as $tag ) {
            $type = ( 'item' == $tag->type ) ? 'product' : 'category';
            $values[] = '( ' . (int) $tag->id . ", '$type', '" . $this->db->escape( $tag->name ) . "' )";
        }

       // Add at up to 500 at a time
        $value_chunks = array_chunk( $values, 500 );

        foreach ( $value_chunks as $vc ) {
            $this->db->query( "INSERT INTO `craigslist_tags` ( `craigslist_tag_id`, `type`, `value` ) VALUES " . implode( ',', $vc ) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)" );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->err( 'Failed to add craigslist tags.', __LINE__, __METHOD__ );
                return false;
            }
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