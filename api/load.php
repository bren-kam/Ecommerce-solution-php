<?php
/**
 * Bootstrap file for setting the ABS_PATH constant
 * and loading the config.php file.
 *
 * Objects:
 * $cache - Cache
 */

/** Define ABS_PATH as the files directory */
define( 'ABS_PATH', str_replace( '/api', '/', $_SERVER['DOCUMENT_ROOT'] ) );

/** Define OPERATING_PATH as the main directory to get things from */
define( 'OPERATING_PATH', ABS_PATH . 'api/' );

/** Define INC_PATH as the includes directory */
define( 'INC_PATH', ABS_PATH . 'includes/' );

/** Define THEME_PATH as the place with all the pages */
define( 'THEME_PATH', ABS_PATH . 'api/theme/' );

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

/** Load global functions */
require INC_PATH . 'functions.php';

/** Load classes */
require INC_PATH . 'classes.php';

/** Dynamic definitions */
define( 'DOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], false ) : 'imagineretailer.com' );
define( 'SUBDOMAIN', ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_X_FORWARDED_HOST'], true ) ) : str_replace( '.' . DOMAIN, '', url::domain( $_SERVER['HTTP_HOST'], true ) ) );

/** Load Objects */
$requests = new Requests();