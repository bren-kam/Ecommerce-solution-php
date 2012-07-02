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
	public static function redirect( $location, $code = 302 ) {
		// HTTP Status code
		header::http_status( $code );

		/* Redirect to new location */
		header::send( "Location: $location" );
	}
	
	/**
	 * Returns the domain of a URL
	 *
	 * @since 1.0
	 *
	 * @param string $url the url
	 * @param bool $subdomains whether to include subdomains or not
	 * @return string
	 */
	public static function domain( $url, $subdomains = true ) {
		// Define variables
		$parse_url = parse_url( trim( $url ) );
		$path_parts = explode( '/', $parse_url['path'], 2 );
		$host = ( isset( $parse_url['host'] ) ) ? $parse_url['host'] : array_shift( $path_parts );
   		$domain = trim( $host );
		
		return ( $subdomains ) ? $domain : preg_replace( '/(?:[-a-zA-Z0-9]+\.)*([-a-zA-Z0-9]+\.[a-zA-Z]{2,3}){1,2}/', '$1', $domain );
	}
	
	/**
	 * Get the Subdomain
	 *
	 * @param $url
	 * @return string
	 */
	public static function subdomain( $url ) {
		return str_replace( '.' . self::domain( $url, false ), '', self::domain( $url ) );
	}
	
	/**
	 * Encrypts a string or array for a post url value
	 *
	 * @param string|array $data the data te be encrypted
	 * @return string
	 */
	public static function encode( $data ) {
		return strtr( base64_encode( addslashes( gzcompress( serialize( $data ), 9 ) ) ), '+/=', '-_,' );
	}

	/**
	 * Decrypts a string or array for a post url value
	 *
	 * @param string|array $data the data te be decrypted
	 * @return string
	 */
	public static function decode( $data ) {
		return unserialize( gzuncompress( stripslashes( base64_decode( strtr( $data, '-_,', '+/=' ) ) ) ) );
	}
	
	/**
	 * Retrieve a modified URL query string.
	 *
	 * You can rebuild the URL and append a new query variable to the URL query by
	 * using this function. You can also retrieve the full URL with query data.
	 *
	 * Adding a single key & value or an associative array. Setting a key value to
	 * emptystring removes the key. Omitting oldquery_or_uri uses the $_SERVER
	 * value.
	 *
	 * @since 1.0
	 *
	 * @param mixed $param1 Either newkey or an associative_array
	 * @param mixed $param2 Either newvalue or oldquery or uri
	 * @param mixed $param3 Optional. Old query or uri
	 * @return string New URL query string.
	 */
	public static function add_query_arg() {
		$ret = '';
		if ( is_array( func_get_arg(0) ) ) {
			$uri = ( @func_num_args() < 2 || false === @func_get_arg( 1 ) ) ? $_SERVER['REQUEST_URI'] : @func_get_arg( 1 );
		} else {
			$uri = ( @func_num_args() < 3 || false === @func_get_arg( 2 ) ) ? $_SERVER['REQUEST_URI'] : @func_get_arg( 2 );
		}
	
		if ( $frag = strstr( $uri, '#' ) ) {
			$uri = substr( $uri, 0, -strlen( $frag ) );
		} else {
			$frag = '';
		}
		
		if ( preg_match( '|^https?://|i', $uri, $matches ) ) {
			$protocol = $matches[0];
			$uri = substr( $uri, strlen( $protocol ) );
		} else {
			$protocol = '';
		}
	
		if ( strpos( $uri, '?' ) !== false ) {
			$parts = explode( '?', $uri, 2 );
			if ( 1 == count( $parts ) ) {
				$base = '?';
				$query = $parts[0];
			} else {
				$base = $parts[0] . '?';
				$query = $parts[1];
			}
		} elseif ( !empty( $protocol ) || strpos( $uri, '=' ) === false ) {
			$base = $uri . '?';
			$query = '';
		} else {
			$base = '';
			$query = $uri;
		}
		
		parse_str( $query, $qs );
		
		if ( get_magic_quotes_gpc() )
			$qs = format::stripslashes_deep( $qs );
		
		$qs = format::urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
		if ( is_array( func_get_arg( 0 ) ) ) {
			$kayvees = func_get_arg( 0 );
			$qs = array_merge( $qs, $kayvees );
		} else {
			$qs[func_get_arg( 0 )] = func_get_arg( 1 );
		}
	
		foreach ( ( array ) $qs as $k => $v ) {
			if ( $v === false )
				unset( $qs[$k] );
		}
	
		$ret = http_build_query( $qs, '', '&' );
		$ret = trim( $ret, '?' );
		$ret = preg_replace( '#=(&|$)#', '$1', $ret );
		$ret = $protocol . $base . $ret . $frag;
		$ret = rtrim( $ret, '?' );
		return $ret;
	}
	
	/**
	 * Removes an item or list from the query string.
	 *
	 * @since 1.0
	 *
	 * @param string|array $key Query key or keys to remove.
	 * @param bool $query When false uses the $_SERVER value.
	 * @return string New URL query string.
	 */
	public static function remove_query_arg( $key, $query=false ) {
		if ( is_array( $key ) ) { // removing multiple keys
			foreach ( $key as $k ) {
				$query = self::add_query_arg( $k, false, $query );
            }

			return $query;
		}
		
		return self::add_query_arg( $key, false, $query );
	}
}