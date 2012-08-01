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
 * Include a controller
 *
 * @param string $path
 * @throws ControllerException
 * @return string
 */
function controller( $path ) {
    // Each path that needs to be checked for a controller
    $controller_paths = array( CONTROLLER_PATH, ABS_PATH . 'controller/' );
    $exists = false;

    foreach ( $controller_paths as $controller_path ) {
        $file_path = $controller_path . $path . '-controller.php';

        if ( $exists = file_exists( $file_path ) )
            break;
    }

    if ( !$exists )
        throw new ControllerException( 'Controller does not exist' );

    // Include the controller
	require $file_path;

    // Instantiate and run
    $controller_name = str_replace( ' ', '', ucwords( str_replace( '-', ' ', basename( $file_path, '.php' ) ) ) );

    $controller = new $controller_name;
    $controller->run();

    return $file_path;
}

/**
 * Choose the method to run
 *
 * @param string $method
 */
function method( $method ) {
    $_REQUEST['_nonce'] = nonce::create( str_replace( '-', '_', $method ) );
}


/**
 * Inc - properly includes a file
 *
 * Properly includes a file and checks the right paths
 *
 * @param string $path
 * @param bool $require [optional]
 * @return string
 */
function lib( $path, $require = true ) {
	$file_path = LIB_PATH . $path . '.php';

	if ( file_exists( $file_path ) ) {
		if ( $require )
			require_once ( $file_path );

		return $file_path;
	}

	return false;
}

/**
 * Library - Includes a library file
 * 
 * Properly includes a file and checks the right paths
 *
 * @param string $path
 * @return bool
 */
function library( $path ) {
	$file_path = LIB_PATH . 'ext/' . $path . '.php';

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
		return true;
	}
	
	return false;
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
 * Securely gets a secure cookie site-wide
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