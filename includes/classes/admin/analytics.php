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
}