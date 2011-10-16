<?php
/**
 * URL class, handles basic url related functions
 *
 * Functions:
 * redirect( string $location [, int $code = 302 ] ) - relative or full URL path
 * string domain( $url ) - gets the domain from a URL
 * @package Studio98 Framework
 * @since 1.0
 */

class url extends Base_Class {
	/**
	 * Redirects to another page with proper header information
	 *
	 * @since 1.0
	 *
	 * @param string $location The new location of the page
	 * @param int $code (Optional) The HTTP Status code, defaults to 302 Found
	 * @returns bool
	 */
	public function redirect( $location, $code = 302 ) {
		// HTTP Status code
		_header::http_status( $code );
		
		/* Redirect to new location */
		_header::send( "Location: $location" );
		exit;
	}
	
	/**
	 * Returns the domain of a URL
	 *
	 * @since 1.0
	 *
	 * @param string $url the url
	 * @returns string
	 */
	public function domain( $url ) {
		$parse_url = parse_url( trim( $url ) );
   		return trim( ( $parse_url['host'] ) ? $parse_url['host'] : array_shift( explode( '/', $parse_url['path'], 2) ) ); 
	}
}