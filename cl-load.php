<?php
/**
 * Bootstrap file for setting the ABS_PATH constant
 * and loading the config.php file.
 *
 * Objects:
 * $cache - Cache
 */

// Should change to imagineretailer.com before deploy
define( 'DEFAULT_DOMAIN', 'greysuitretail.com' );

// Set default timezone
date_default_timezone_set('America/Chicago');

/** Define LIVE if the website is live */
define( 'LIVE', false );

/** Define ABS_PATH as the files directory */
define( 'ABS_PATH', __DIR__ . '/' );

// Hold the library path
define( 'LIB_PATH', ABS_PATH . 'lib/' );

// Set CLI
define( 'CLI', true );

function gsr_error_handler() {
    $error = error_get_last();

    if ( is_null( $error ) )
        return;

    // Get Errors
    $error_types = array(
        1 => 'E_ERROR'
        , 2 => 'E_WARNING'
        , 4 => 'E_PARSE'
        , 8 => 'E_NOTICE'
        , 16 => 'E_CORE_ERROR'
        , 32 => 'E_CORE_WARNING'
        , 64 => 'E_COMPILE_ERROR'
        , 128 => 'E_COMPILE_WARNING'
        , 256 => 'E_USER_ERROR'
        , 512 => 'E_USER_WARNING'
        , 1024 => 'E_USER_NOTICE'
        , 2048 => 'E_STRICT'
        , 4096 => 'E_RECOVERABLE_ERROR'
        , 8192 => 'E_DEPRECATED'
        , 16384 => 'E_USER_DEPRECATED'
        , 32767 => 'E_ALL'
    );

    // Setup Message
    $date = new DateTime();
    $message = $date->format( 'Y-m-d H:i:s' ) . ': ';
    $message .= $error_types[$error['type']] . ' - ';
    $message .= $error['message'];
    $message .= ' (' . $error['file'] . ' #' . $error['line'] . ')';

    error_log( $message, 3, '/gsr/cl/errors.log' );

    require ABS_PATH . 'view/cl/fatal-error.php';
}

register_shutdown_function( 'gsr_error_handler' );

/** Include Studio98 library */
require LIB_PATH . 'ext/s98lib/init.php';


/** Dynamic definitions */
define( 'DOMAIN', 'local-greysuitretail.com' );
define( 'SUBDOMAIN', 'admin' );

// Define MVC paths
define( 'MODEL_PATH', ABS_PATH . 'model/' . SUBDOMAIN . '/' );
define( 'VIEW_PATH', ABS_PATH . 'view/' . SUBDOMAIN . '/' );
define( 'CONTROLLER_PATH', ABS_PATH . 'controller/' . SUBDOMAIN . '/' );
define( 'CACHE_PATH', ABS_PATH . 'cache/' );
define( 'TMP_PATH', ABS_PATH . 'tmp/' );

/** Load global functions */
require LIB_PATH . '/misc/functions.php';

/** Load Cookie Definitions */

define( 'COOKIE_ABBR', 'gsr_' );
define( 'COOKIE_HASH', md5( 'http://www.' . DOMAIN . '.com/' ) );
define( 'AUTH_COOKIE', 'auth_' . COOKIE_HASH );
define( 'SECURE_AUTH_COOKIE', 'sec_auth_' . COOKIE_HASH );

// Set the domain to the domain
ini_set( 'session.cookie_domain', '.' . DOMAIN );

/** Load Objects */

/** Including the label information */
require LIB_PATH . 'labels/' . DOMAIN . '.php';

/** Load the Base Controller */
require ABS_PATH . 'controller/base-controller.php';

/** Load the Loaders */
require LIB_PATH . 'misc/loaders.php';

/** Routing */
require ABS_PATH . 'routing.php';
