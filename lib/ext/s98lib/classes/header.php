<?php
/**
 * Handles all header information
 *
 * Functions:
 * http_status( $code ) - the http_status code
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class header extends Base_Class {
	/**
	 * The different content types and their header information
	 * @var array
	 */
	public static $content_types = array (
		'json' => 'application/json'
        , 'text' => 'text/plain'
        , 'csv' => 'application/octet-stream'
        , 'xml' => 'text/xml'
        , 'gif' => 'image/gif'
        , 'jpg' => 'image/jpeg'
        , 'png' => 'image/png'
        , 'css' => 'text/css; charset=utf-8'
        , 'js' => 'text/javascript; charset=utf-8'
        , 'javascript' => 'text/javascript; charset=utf-8'
	);
	
	/**
	 * Sends a header
	 *
	 * @since 1.0
	 *
	 * @param string|array $header The header(s) you want to send
	 * @returns bool
	 */
	public static function send( $header ) {
		if ( is_array( $header ) ) {
			foreach ( $header as $k => $v ) {
				header( "$k: $v" );
			}
		} else {
			header( $header );
		}
	}
	
	/**
	 * Sends a header for a content type
	 *
	 * @since 1.0
	 *
	 * @param string $type The type of content
     * @return bool;
	 */
	public static function type( $type ) {
        // We don't want to do JSON if the browser doesn't support it
        if ( 'json' == $type ) {
            // Set the header if it's not IE8 (IE8 is stupid and doesn't recognize json/application header types)
            $browser = fn::browser();

            if ( 'Msie' == $browser['name'] && version_compare( 8, $browser['version'], '<=' ) )
                return false;
        }

		$content_type = self::$content_types[$type];
		
		if ( !empty( $content_type ) )
			header( 'Content-type: ' . $content_type );

        return true;
	}
	
	/**
	 * Sets the headers to not cache this page
	 *
	 * @since 1.0
	 */
	public static function no_cache() {
		self::send( array(
			'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
			'Last-Modified' => gmdate( 'D, d M Y H:i:s' ) . ' GMT',
			'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
			'Pragma' => 'no-cache'
		) );
	}

	/**
	 * Sets the headers to not cache this page
	 *
	 * @param int $seconds (optional|60)
	 */
	public static function cache( $seconds = 60 ) {
		// Greenwich Mean Time
		$gmt = gmdate( 'D, d M Y H:i:s', time() + $seconds );

		self::send( array(
			'Cache-Control' => "public, max-age=$seconds"
			, 'Expires' => "$gmt GMT"
			, 'Last-Modified' =>  "$gmt GMT"
		) );
	}
	/**
	 * Sets the headers for css
	 *
	 * @since 1.0
	 */
	public static function css() {
		self::send( array( 
			'Content-Type' => 'text/css; charset=utf-8',
			'Vary' => 'Accept-Encoding',
			'Expires' => gmdate( "D, d M Y H:i:s", time() + 864000 ) . ' GMT'
		) );
	}
	
	/**
	 * Sets the headers for javascript
	 *
	 * @since 1.0
	 */
	public static function javascript() {
		self::send( array( 
			'Content-Type' => 'text/javascript; charset=utf-8',
			'Vary' => 'Accept-Encoding',
			'Expires' => gmdate( "D, d M Y H:i:s", time() + 864000 ) . ' GMT'
		) );
	}

    /**
     * Sets the headers for an image
     *
     * @param string $extension
     * @return bool
     */
    public static function image( $extension ) {
        if ( !isset( self::$content_types[$extension] ) )
            return false;

        return self::type( $extension );
    }

    /**
     * Send Download Header
     *
     * @param string $file_name
     */
    public static function download( $file_name ) {
        self::send( array(
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"'
        ));
    }
	
	/**
	 * Sends a header of an HTTP Status code
	 *
	 * @since 1.0
	 *
	 * @param int $code The HTTP Status code
	 * @returns bool
	 */
	public static function http_status( $code ) {
		$http = array (
		   100 => 'HTTP/1.1 100 Continue',
		   101 => 'HTTP/1.1 101 Switching Protocols',
		   200 => 'HTTP/1.1 200 OK',
		   201 => 'HTTP/1.1 201 Created',
		   202 => 'HTTP/1.1 202 Accepted',
		   203 => 'HTTP/1.1 203 Non-Authoritative Information',
		   204 => 'HTTP/1.1 204 No Content',
		   205 => 'HTTP/1.1 205 Reset Content',
		   206 => 'HTTP/1.1 206 Partial Content',
		   300 => 'HTTP/1.1 300 Multiple Choices',
		   301 => 'HTTP/1.1 301 Moved Permanently',
		   302 => 'HTTP/1.1 302 Found',
		   303 => 'HTTP/1.1 303 See Other',
		   304 => 'HTTP/1.1 304 Not Modified',
		   305 => 'HTTP/1.1 305 Use Proxy',
		   307 => 'HTTP/1.1 307 Temporary Redirect',
		   400 => 'HTTP/1.1 400 Bad Request',
		   401 => 'HTTP/1.1 401 Unauthorized',
		   402 => 'HTTP/1.1 402 Payment Required',
		   403 => 'HTTP/1.1 403 Forbidden',
		   404 => 'HTTP/1.1 404 Not Found',
		   405 => 'HTTP/1.1 405 Method Not Allowed',
		   406 => 'HTTP/1.1 406 Not Acceptable',
		   407 => 'HTTP/1.1 407 Proxy Authentication Required',
		   408 => 'HTTP/1.1 408 Request Time-out',
		   409 => 'HTTP/1.1 409 Conflict',
		   410 => 'HTTP/1.1 410 Gone',
		   411 => 'HTTP/1.1 411 Length Required',
		   412 => 'HTTP/1.1 412 Precondition Failed',
		   413 => 'HTTP/1.1 413 Request Entity Too Large',
		   414 => 'HTTP/1.1 414 Request-URI Too Large',
		   415 => 'HTTP/1.1 415 Unsupported Media Type',
		   416 => 'HTTP/1.1 416 Requested range not satisfiable',
		   417 => 'HTTP/1.1 417 Expectation Failed',
		   500 => 'HTTP/1.1 500 Internal Server Error',
		   501 => 'HTTP/1.1 501 Not Implemented',
		   502 => 'HTTP/1.1 502 Bad Gateway',
		   503 => 'HTTP/1.1 503 Service Unavailable',
		   504 => 'HTTP/1.1 504 Gateway Time-out'
		);

		header( $http[$code] );
	}
}