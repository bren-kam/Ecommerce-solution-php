<?php
/**
 * Functions
 *
 * Contains basic functions that can be used anywhere
 *
 * @package Grey Suit Retail
 * @since 1.0
 */

/**
 * Inc - properly includes a file
 * 
 * Properly includes a file and checks the right paths
 *
 * @param string $file
 * @param bool $require [optional]
 * @return string
 */
function inc( $file, $require = true ) {
	$file_path = INC_PATH . $file . '.php';
	
	if ( file_exists( $file_path ) ) {
		if ( $require )
			require( $file_path );
		
		return $file_path;
	}
	
	return false;
}

/**
 * Library - Includes a library file
 * 
 * Properly includes a file and checks the right paths
 *
 * @param string $file
 * @return bool
 */
function library( $file ) {
	$file_path = INC_PATH . 'libraries/' . $file . '.php';

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
		return true;
	}
	
	return false;
}

/**
 * Module - Includes a module
 * 
 * Properly includes a file and checks the right paths
 *
 * @param string $file
 * @return bool
 */
function module( $file ) {
	$file_path = INC_PATH . 'modules/' . $file . '.php';
	
	if ( file_exists( $file_path ) ) {
		require $file_path;
		return true;
	}
	
	return false;
}

/**
 * Theme Inc - properly includes a theme file
 * 
 * Properly includes a file and checks the right paths
 *
 * @param string $file
 * @param bool $require (optional) whether it should be included
 * @return string|bool
 */
function theme_inc( $file, $require = false ) {
	$file_path = THEME_PATH . $file . '.php';
	
	if ( file_exists( $file_path ) ) {
		if ( $require )
            require $file_path;

		return $file_path;
	}
	
	return false;
}


/**
 * Find out what something is
 *
 * @param string $key
 * @return bool
 */
function is( $key ) {
	global $type;
	$browser = fn::browser();
	
	switch ( $key ) {
		case 'ie8':
			return ( 'Msie' == $browser['name'] && version_compare( '7', $browser['version'], '<' ) ) ? true : false;
		break;
		
		default:
			return ( $key == $type ) ? true : false;
		break;
	}
}

/**
 * Starts the timer, for debugging purposes.
 *
 * @since 1.0.0
 *
 * @return true
 */
function timer_start() {
	return microtime( true );
}

/**
 * Stops the debugging timer.
 *
 * @since 1.0.0
 *
 * @param int $time_start
 * @return int Total time spent on the query, in milliseconds
 */
function timer_stop( $time_start ) {
	return microtime( true ) - $time_start;
}

/**
 * Securely sets a secure cookie site-wide
 *
 * @param string $name the name of the cookie (defined in load.php)
 * @param string $value the value of the cookie
 * @param string $expire how long the cookie should last
 */
function set_cookie( $name, $value, $expire ) {
	$secure = false;

    setcookie( COOKIE_ABBR . $name, $value, time() + $expire, '/', '.' . DOMAIN, $secure, true );
    setcookie( COOKIE_ABBR . $name, $value, time() + $expire, '/', '.' . SUBDOMAIN . '.' . DOMAIN, $secure, true );

    // If it's set on the admin side, we also want to set it on the account side.
    if ( stristr( SUBDOMAIN, 'admin' ) )
        setcookie( COOKIE_ABBR . $name, $value, time() + $expire, '/', '.' . str_replace( 'admin', 'account', SUBDOMAIN ) . '.' . DOMAIN, $secure, true );
}

/**
 * Securely sets a secure cookie site-wide
 *
 * @param string $name
 * @return string
 */
function get_cookie( $name ) {
	return ( isset( $_COOKIE[COOKIE_ABBR . $name] ) ) ? $_COOKIE[COOKIE_ABBR . $name] : false;
}

/**
 * Removes a cookie
 *
 * @param string $name the name of the cookie (defined in load.php)
 */
function remove_cookie( $name ) {
    setcookie( COOKIE_ABBR . $name, ' ', time() - 31536000, '/', '.' . DOMAIN );
	setcookie( COOKIE_ABBR . $name, ' ', time() - 31536000, '/', '.' . SUBDOMAIN . '.' . DOMAIN );

    // If it's set on the admin side, we also want to set it on the account side.
    if ( stristr( SUBDOMAIN, 'admin') )
        setcookie( COOKIE_ABBR . $name, ' ', time() - 31536000, '/', '.' . str_replace( 'admin', 'account', SUBDOMAIN ) . '.' . DOMAIN, $secure, true );
}