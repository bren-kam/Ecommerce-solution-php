<?php
/**
 * Routing
 *
 * Determines what to display
 *
 * @package Real Statistics
 * @since 1.0
 */

// If it's the home page
if ( '/' == str_replace( '?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] ) ) {
    // Define the method
    method('index');

    // Set the transaction name
    $transaction_name = controller('home');
} else {
	// We know it's not the home page, continue
	
	// Force a trailing slash
	if ( $_SERVER['REQUEST_URI'][strlen( $_SERVER['REQUEST_URI'] ) - 1] != '/' ) {
		// Query string position
		$qs_pos = strpos( $_SERVER['REQUEST_URI'], '?' );
	
		// If there is a query string, redirect with the query string
		if ( $qs_pos > 0 ) {
			if ( $_SERVER['REQUEST_URI'][$qs_pos - 1] != '/' )
				url::redirect( $_SERVER['REDIRECT_URL'] . '/?' . $_SERVER['QUERY_STRING'] );
		} else {
			url::redirect( $_SERVER['REQUEST_URI'] . '/' );
		}
	}
	
	// We need to get the slug parts
	$slug_parts = explode( '/', preg_replace( '/\/([^?]+)\/(?:\?.*)?/', '$1', str_replace( '?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] ) ) );
	
	// Find out what method we need to use
	$method = ( 1 == count( $slug_parts ) ) ? 'index' : array_pop( $slug_parts );
    method( $method );

    try {
        $transaction_name = controller( implode( '/', $slug_parts ) );
    } catch ( ControllerException $e ) {
        method('http_404');
        $transaction_name = controller( 'error' );
    }
}

if ( extension_loaded( 'newrelic' ) )
    newrelic_name_transaction( str_replace( ABS_PATH, '', $transaction_name ) );