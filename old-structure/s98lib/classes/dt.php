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

if ( !isset( $s98_cache ) ) {
	global $s98_cache;
	$s98_cache = new Base_Cache();
}

class dt extends Base_Class {
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
     * Hold an instantiated date time
     * @var DateTime
     */
    public static $datetime;
	
	/**
	 * Cached date function
	 * 
	 * @since 1.0
	 *
	 * @param string $format the format for the date
	 * @param int $timestamp the timestamp
	 * @return string
	 */
	public static function date( $format, $timestamp = -1 ) {
        $datetime = self::get_datetime();

		global $s98_cache;
		
		// Check to see if it's human readable
		$human_readable = array_key_exists( $format, self::$date_time_formats );
		
		if ( $human_readable )
			$format = self::$date_time_formats[$format];
		
		$date = $s98_cache->get( $format . $timestamp, 'date' );
		
		if ( empty( $date ) ) {
			if ( -1 != $timestamp )
				$datetime->setTimestamp( $timestamp );
			
			$date = $datetime->format( $format );

            // Reset it
			if ( -1 != $timestamp )
                $datetime->setTimestamp( time() );

			
			$s98_cache->add( $format . $timestamp, $date, 'date' );
		}

		return $date;
	}

    /**
     * Get the date time
     * @static
     * @return DateTime
     */
    public static function get_datetime() {
        if ( !self::$datetime instanceof DateTime )
            self::$datetime = new DateTime();

        return self::$datetime;
    }
	
	/**
	 * Same as the doing dt::date('now')
	 * 
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function now() {
		return self::date('now');
	}
	
	/**
	 * Turns seconds into time
	 *
	 * @param int $seconds
	 * @return string
	 */
	public static function sec_to_time( $seconds ) {
		$hours = floor( $seconds / 3600 );
		$minutes = floor( $seconds % 3600 / 60 );
		$seconds = $seconds % 60;
		
		return sprintf( "%02d:%02d:%02d", $hours, $minutes, $seconds );
	}

    /**
     * Adjust a timezone for a date
     *
     * @param string $date
     * @param string $timezone
     * @param string $new_timezone
     * @param string $format
     * @return string
     */
    public static function adjust_timezone( $date, $timezone, $new_timezone = NULL, $format = 'Y-m-d H:i:s' ) {
        // Make sure we have a default timezone
        if ( is_null( $new_timezone ) )
            $new_timezone = DEFAULT_TIMEZONE;

        // Create Date Object
        $datetime = new DateTime( $date, new DateTimeZone( $timezone ) );

        // Adjust the timezone
        $datetime->setTimezone( new DateTimeZone( $new_timezone ) );

        // Return the format
        return $datetime->format( $format );
    }
}