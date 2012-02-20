<?php
/**
 * Bootstrap file for setting the ABS_PATH constant
 * and loading the config.php file.
 *
 * Objects:
 * $cache - Cache
 */

define( 'PROFILE', isset( $_GET['profile'] ) && '1' == $_GET['profile'] );

/** Define LIVE if the website is live */
define( 'LIVE', false );

/** Let other parts of the system know this is not the account section */
define( 'ACCOUNT', false );

/** Let other parts of the system know this is an admin section */
define( 'ADMIN', true );

/** Let other parts of the system know this is not the apps section */
define( 'APPS', false );

/** Define ABS_PATH as the files directory */
define( 'ABS_PATH', str_replace( '/admin', '/', $_SERVER['DOCUMENT_ROOT'] ) );

/** Define OPERATING_PATH as the main directory to get things from */
define( 'OPERATING_PATH', ABS_PATH . 'admin/' );

/** Define INC_PATH as the includes directory */
define( 'INC_PATH', ABS_PATH . 'includes/' );

/** Define THEME_PATH as the place with all the pages */
define( 'THEME_PATH', ABS_PATH . 'admin/theme/' );

if ( PROFILE ) {
	// Enable XHProf (profiler)
	require '/home/imaginer/xhprof_lib/utils/xhprof_lib.php';
	require '/home/imaginer/xhprof_lib/utils/xhprof_runs.php';
	xhprof_enable( XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY );
}

// Show us the errors
if ( defined('E_RECOVERABLE_ERROR') ) {
    error_reporting( E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
} else {
    error_reporting( E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
}


/** Include Studio98 library */
require_once ABS_PATH . 's98lib/init.php';

/** Load global functions */
require_once INC_PATH . 'functions.php';

/** Load classes */
require_once INC_PATH . 'classes.php';

/** Error Handler */
//$e = new Error_Handler();

/** Dynamic definitions */
define( 'DOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], false ) : 'imagineretailer.com' );
define( 'SUBDOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], true ) ) : str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_HOST'], true ) ) );

/** Load Cookie Definitions */
// Used to guarantee unique hash cookies
define( 'COOKIE_HASH', md5( 'http://www.' . DOMAIN . '.com/' ) );

// The Cookie for the authorization in a insecure environment
define( 'AUTH_COOKIE', 'auth_' . COOKIE_HASH );

// The Cookie for the authorization in a insecure environment
define( 'SECURE_AUTH_COOKIE', 'sec_auth_' . COOKIE_HASH );

// Set the domain to the domain
ini_set( 'session.cookie_domain', '.' . DOMAIN );
ini_set('display_errors', 'STDOUT');
/** Load Objects */

$mc = new Memcache_Wrapper;
$t = new Template();
$u = new Users();

// We don't want to redeclare it
if ( !isset( $s98_cache ) )
	$s98_cache = new Base_Cache();

$cache = &$s98_cache; // Setting up a point to all cache functions

/** Including the label information */
require_once INC_PATH . 'labels/' . DOMAIN . '.php';

/** Routing */
require_once OPERATING_PATH . 'routing.php' ;


if ( PROFILE ) {
	// End XHProf and save query
	$profiler_namespace = 'admin.imagineretailer.com';  // namespace for your application
	$xhprof_data = xhprof_disable();
	$xhprof_runs = new XHProfRuns_Default();
	$run_id = $xhprof_runs->save_run( $xhprof_data, $profiler_namespace );

	// url to the XHProf UI libraries (change the host name and path)
	$profiler_url = sprintf('http://admin.imagineretailer.com/xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
	echo '<a href="'. $profiler_url .'" target="_blank">Profiler output</a>';
}