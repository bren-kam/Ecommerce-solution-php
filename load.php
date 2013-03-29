<?php
/**
 * Bootstrap file for setting the ABS_PATH constant
 * and loading the config.php file.
 *
 * Objects:
 * $cache - Cache
 */

define( 'PROFILE', isset( $_GET['profile'] ) && '1' == $_GET['profile'] );

// Should change to imagineretailer.com before deploy
define( 'DEFAULT_DOMAIN', 'imagineretailer.com' );

/** Define LIVE if the website is live */
define( 'LIVE', false );

// Show us the errors if we're not live
if ( LIVE ) {
    error_reporting(0);
} else {
    //error_reporting(0);
    error_reporting( E_ALL ^ E_NOTICE );
}

/** Error Handler */
//if ( extension_loaded('newrelic') )
    //set_error_handler( 'newrelic_notice_error' );

/** Define ABS_PATH as the files directory */
define( 'ABS_PATH', realpath( __DIR__ ) . '/' );

// Hold the library path
define( 'LIB_PATH', ABS_PATH . 'lib/' );

/** Include Studio98 library */
require LIB_PATH . 'ext/s98lib/init.php';

/** Dynamic definitions */
define( 'DOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], false ) : DEFAULT_DOMAIN );

// Define the subdomain
$subdomain = ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], true ) ) : str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_HOST'], true ) );
list( $subdomain ) = explode ( '.', $subdomain );

define( 'SUBDOMAIN', $subdomain );

unset( $subdomain );

// Define MVC paths
define( 'MODEL_PATH', ABS_PATH . 'model/' . SUBDOMAIN . '/' );
define( 'VIEW_PATH', ABS_PATH . 'view/' . SUBDOMAIN . '/' );
define( 'CONTROLLER_PATH', ABS_PATH . 'controller/' . SUBDOMAIN . '/' );
define( 'CACHE_PATH', ABS_PATH . 'cache/' );
define( 'TMP_PATH', ABS_PATH . 'tmp/' );

if ( PROFILE && extension_loaded( 'xhprof' ) ) {
	// Enable XHProf (profiler)
	require LIB_PATH . 'xhprof_lib/utils/xhprof_lib.php';
	require LIB_PATH . 'xhprof_lib/utils/xhprof_runs.php';
    xhprof_enable( XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY );
}

/** Load global functions */
require LIB_PATH . '/misc/functions.php';

/** Load Cookie Definitions */

define( 'COOKIE_ABBR', 'gsr_' );
define( 'COOKIE_HASH', md5( 'http://www.' . DOMAIN . '.com/' ) );
define( 'AUTH_COOKIE', 'auth_' . COOKIE_HASH );
define( 'SECURE_AUTH_COOKIE', 'sec_auth_' . COOKIE_HASH );

// Set the domain to the domain
ini_set( 'session.cookie_domain', '.' . DOMAIN );
ini_set('display_errors', 'STDOUT');

/** Load Objects */

/** Including the label information */
require LIB_PATH . 'labels/' . DOMAIN . '.php';

/** Load the Base Controller */
require ABS_PATH . 'controller/base-controller.php';

/** Load the Loaders */
require LIB_PATH . 'misc/loaders.php';

// Clear all posts of slashes!
if ( isset( $_POST ) && 0 != count( $_POST ) )
    $_POST = format::stripslashes_deep( $_POST );

/** Routing */
require ABS_PATH . 'routing.php';

if ( PROFILE && extension_loaded('xhprof') ) {
	// End XHProf and save query
	$profiler_namespace = 'Grey Suit Retail';  // namespace for your application
	$xhprof_data = xhprof_disable();
	$xhprof_runs = new XHProfRuns_Default();
	$run_id = $xhprof_runs->save_run( $xhprof_data, $profiler_namespace );

	// url to the XHProf UI libraries (change the host name and path)
	$profiler_url = sprintf('http://admin.imagineretailer.com/xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
	echo '<a href="'. $profiler_url .'" target="_blank">Profiler output</a>';
}