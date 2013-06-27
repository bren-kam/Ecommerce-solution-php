<?php
/**
 * Routing
 *
 * Determines what to display
 *
 * @package Real Statistics
 * @since 1.0
 */

if ( isset( $argv ) ) {
    if ( !isset( $argv[1] ) ) {
        // It's command line, we need a path ("crons/daily/")
        trigger_error( 'No CL Argument', E_USER_ERROR );
    } else {
        // Create artificial query
        $url = parse_url( 'http://' . SUBDOMAIN . '.' . DOMAIN . '/' . $argv[1] );

        $_SERVER['REQUEST_URI'] = $url['path'];

        if ( isset( $url['query'] ) ) {
            $_SERVER['QUERY_STRING'] = $url['query'];
            $_SERVER['REQUEST_URI'] .= '?' . $url['query'];
        }

        unset( $url );
    }
}

// If it's the home page
if ( '/' == str_replace( '?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] ) ) {
    // Set the transaction name
    $transaction_name = controller( 'home', 'index' );
} else {
    $need_controller = true;

	// Force a trailing slash
	if ( $_SERVER['REQUEST_URI'][strlen( $_SERVER['REQUEST_URI'] ) - 1] != '/' ) {
        if ( '/images/' == substr( $_SERVER['REQUEST_URI'], 0, 8 ) ) {
            // It's an image!
            $method = 'image';
            $controller = 'resources';

        } elseif ( '/media/' == substr( $_SERVER['REQUEST_URI'], 0, 7 ) ) {
            // It's an image!
            $method = 'media';
            $controller = 'resources';

        }  elseif ( '/ckeditor/' == substr( $_SERVER['REQUEST_URI'], 0, 10 ) ) {
            // It's an image!

            $method = 'external';
            $controller = 'resources';
        } else {
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
	}

    if ( !isset( $controller ) ) {
        // We need to get the slug parts
        $slug_parts = explode( '/', preg_replace( '/\/([^?]+)\/(?:\?.*)?/', '$1', str_replace( '?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] ) ) );

        $controller = implode( '/', $slug_parts );

        try {
            $transaction_name = controller( $controller, 'index' );
            $need_controller = false;
        } catch ( ControllerException $e ) {
            $need_controller = true;

            $slug_parts = explode( '/', $controller );

            // Find out what method we need to use
            $method = ( 1 == count( $slug_parts ) ) ? 'index' : array_pop( $slug_parts );

            // Make them proper
            $controller = implode( '/', $slug_parts );
        }
    }

    // If Still need to include
    if ( $need_controller ) {
        $method = str_replace( '-', '_', $method );

        // Try again
        try {
            $transaction_name = controller( $controller, $method );
        } catch ( ControllerException $e ) {
            $transaction_name = controller( 'error', 'http_404' );
        }
    }
}

if ( extension_loaded( 'newrelic' ) )
    newrelic_name_transaction( str_replace( ABS_PATH, '', $transaction_name ) );