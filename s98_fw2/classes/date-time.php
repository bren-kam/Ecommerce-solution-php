<?php
/**
 * Date-time class, gets and caches date and time functions
 *
 * Functions:
 * string date ( string $format [, int $timestamp ] )
 *
 * @package Studio98 Framework
 * @since 1.0
 */

if( !isset( $s98_cache ) ) {
	global $s98_cache;
	$s98_cache = new Base_Cache();
}

class date_time extends Base_Class {
	/**
	 * An array to hold human readable date formats
	 *
	 * static public
	 */
	public static $date_time_formats = array (
		'date' => 'Y-m-d'
		, 'datetime' => 'Y-m-d H:i:s'
		, 'today' => 'Y-m-d H:i:s'
		, 'now' => 'Y-m-d H:i:s'
		, 'year' => 'Y'
		, 'month' => 'm'
		, 'day' => 'd'
		, 'hour' => 'H'
		, 'minute' => 'i'
		, 'second' => 's'
		, 'day_of_week' => 'l'
		, 'month_name' => 'F'
		, 'month_abbr' => 'M'
	);
	
	/**
	 * Cached date function
	 * 
	 * @since 1.0
	 *
	 * @param string $format the format for the date
	 * @param int $timestamp the timestamp
	 * @return string
	 */
	public function date( $format, $timestamp = -1 ) {
		global $s98_cache;
		
		// Check to see if it's human readable
		$human_readable = array_key_exists( $format, self::$date_time_formats );
		
		if( $human_readable )
			$format = self::$date_time_formats[$format];
		
		$date = $s98_cache->get( $format . $timestamp, 'date' );
		
		if( empty( $date ) ) {
			if( -1 == $timestamp )
				$timestamp = time();
			
			$date = date( $format, $timestamp );
			
			$s98_cache->add( $format . $timestamp, $date, 'date' );
		}
		
		return $date;
	}
	
	/**
	 * Same as the doing date_time::date('now')
	 * 
	 * @since 1.0
	 *
	 * @return string
	 */
	public function now() {
		return self::date('now');
	}
	
	/**
	 * Turns seconds into time
	 *
	 * @param int $seconds
	 * @return string
	 */
	public function sec_to_time( $seconds ) {
		$hours = floor( $seconds / 3600 );
		$minutes = floor( $seconds % 3600 / 60 );
		$seconds = $seconds % 60;
		
		return sprintf( "%d:%02d:%02d", $hours, $minutes, $seconds );
	} 
}