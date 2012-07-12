<?php
/**
 * Bootstrap file for setting the ABS_PATH constant
 * and loading the config.php file.
 *
 * Objects:
 * $cache - Cache
 */

/** Define LIVE if the website is live */
define( 'LIVE', false );

/** Let other parts of the system know this is an admin section */
define( 'ADMIN', false );

/** Define ABS_PATH as the files directory */
define( 'ABS_PATH', '/gsr/systems/backend/' );

/** Define OPERATING_PATH as the main directory to get things from */
define( 'OPERATING_PATH', ABS_PATH . 'feed/' );

/** Define INC_PATH as the includes directory */
define( 'INC_PATH', ABS_PATH . 'includes/' );

/** Define THEME_PATH as the place with all the pages */
define( 'THEME_PATH', ABS_PATH . 'feed/theme/' );

// Show us the errors
if ( defined('E_RECOVERABLE_ERROR') ) {
    error_reporting( E_ERROR | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
} else {
    error_reporting( E_ERROR | E_PARSE | E_USER_ERROR | E_USER_WARNING );
}

/** Error Handler */
if ( extension_loaded('newrelic') )
    set_error_handler( 'newrelic_notice_error' );

/** Include Studio98 framework */
require ABS_PATH . 's98lib/init.php';

/** Load Cookie Definitions */

// Create a cookie abbreviation
define( 'COOKIE_ABBR', 'gsr_' );

// Used to guarantee unique hash cookies
define( 'COOKIE_HASH', md5( 'http://feed.imagineretailer.com/' ) );

// The Cookie for the authorization in a insecure environmen
define( 'AUTH_COOKIE', 'auth_' . COOKIE_HASH );

// The Cookie for the authorization in a insecure environmen
define( 'SECURE_AUTH_COOKIE', 'sec_auth_' . COOKIE_HASH );

/** Load global functions */
require INC_PATH . 'functions.php';

/** Load classes */
require INC_PATH . 'classes.php';

/** Dynamic definitions */
define( 'DOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], false ) : 'imagineretailer.com' );
define( 'SUBDOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], true ) ) : str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_HOST'], true ) ) );

/** Load Objects */
$mc = new Memcache_Wrapper;

// We don't want to redeclare it
if( !isset( $s98_cache ) )
	$s98_cache = new Base_Cache();

$cache = &$s98_cache; // Setting up a point to all cache functions


/** Including the label information */
require INC_PATH . 'labels/' . DOMAIN . '.php';

$feed = new Feed_API;