<?php
/**
 * URL class, handles basic url related functions
 *
 * Functions:
 * redirect( $location, $http_status ) - relative or full URL path
 *
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
}