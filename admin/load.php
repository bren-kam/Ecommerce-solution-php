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

/** Let other parts of the system know this is not the account section */
define( 'ACCOUNT', false );

/** Let other parts of the system know this is an admin section */
define( 'ADMIN', true );

/** Let other parts of the system know this is not the apps section */
define( 'APPS', false );


/** Define ABS_PATH as the files directory */
define( 'ABS_PATH', '/home/imaginer/public_html/' );

/** Define OPERATING_PATH as the main directory to get things from */
define( 'OPERATING_PATH', '/home/imaginer/public_html/admin/' );

/** Define INC_PATH as the includes directory */
define( 'INC_PATH', '/home/imaginer/public_html/includes/' );

/** Define THEME_PATH as the place with all the pages */
define( 'THEME_PATH', '/home/imaginer/public_html/admin/theme/' );

// Show us the errors
if( !DEBUG ) {
	if ( defined('E_RECOVERABLE_ERROR') ) {
		error_reporting( E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	} else {
		error_reporting( E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
	}
}


/** Include Studio98 framework */
require_once( ABS_PATH . 's98_fw2/init.php' );

/** Load Cookie Definitions */
// Used to guarantee unique hash cookies
define( 'COOKIE_HASH', md5( 'http://' . DOMAIN . '/' ) );

// The Cookie for the authorization in a insecure environmen
define( 'AUTH_COOKIE', 'auth_' . COOKIE_HASH );

// The Cookie for the authorization in a insecure environmen
define( 'SECURE_AUTH_COOKIE', 'sec_auth_' . COOKIE_HASH );

/** Load global functions */
require_once( INC_PATH . 'functions.php' );

/** Load classes */
require_once( INC_PATH . 'classes.php' );

/** Dynamic definitions */
define( 'DOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], false ) : 'imagineretailer.com' );

// Set the domain to the domain
ini_set( 'session.cookie_domain', '.' . DOMAIN );

/** Load Objects */
$mc = new Memcache_Wrapper;
$t = new Template();
$u = new Users();

// We don't want to redeclare it
if( !isset( $s98_cache ) )
	$s98_cache = new Base_Cache();

$cache = &$s98_cache; // Setting up a point to all cache functions

/** Including the label information */
require_once( INC_PATH . 'labels/' . DOMAIN . '.php' );

/** Routing */
require_once( ABS_PATH . 'routing.php' );

