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
	$type = 'home';
    
    // Set the transaction name
    $transaction_name = theme_inc( 'index' );
    
	require $transaction_name;
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
	
	// Find out what we need to do
	if ( count( $slug_parts ) > 1 ) {
		// We know that it is not a page, so we don't have to check for it
		switch ( $slug_parts[0] ) {
			case 'ajax':
				$type = 'ajax';
				
				// Get jQuery library
				library('jQuery');
				
				// Form the ajax file
				$combined_slug_parts = array_shift( $slug_parts );
				$ajax_file = implode( '/', $slug_parts );
				
                // Set the transaction name
				$ajax_path = $transaction_name = OPERATING_PATH . "ajax/$ajax_file.php";
				
				if ( is_file( $ajax_path ) ) {
					require $ajax_path;
				} else {
					$type = '404';
				
					// This page was not found
					header::http_status( 404 );
                    
                    // Set the transaction name
					$transaction_name = theme_inc( '404' );
                    
					// The page they are looking for can't be found
					require $transaction_name;
				}
			break;

			case 'dialogs':
				$type = 'dialog';
				
				// Form the ajax file
				$combined_slug_parts = array_shift( $slug_parts );
				$dialog_file = implode( '/', $slug_parts );
				
                // Set the transaction name
				$dialog_path = $transaction_name = OPERATING_PATH . "dialogs/$dialog_file.php";
				
				if ( is_file( $dialog_path ) ) {
					require $dialog_path;
				} else {
					$type = '404';
				
					// This page was not found
					header::http_status( 404 );
						
					// Set the transaction name
					$transaction_name = theme_inc( '404' );
                    
					// The page they are looking for can't be found
					require $transaction_name;
				}
			break;
			
			default:
				// This gets rid of any initial or ending /
				$smart_slug_parts = implode( '/', $slug_parts );
				
				// Find out if it's just a sub folder of something
				if ( is_file( THEME_PATH . $smart_slug_parts . '.php' ) ) {
					$type = 'page';
					
                    // Set the transaction name
                    $transaction_name = theme_inc( $smart_slug_parts );
                    
					// This is a normal page
					require $transaction_name;
				}  elseif ( is_file( THEME_PATH . $smart_slug_parts . '/index.php' ) ) {
					$type = 'page';
					
                    // Set the transaction name
                    $transaction_name = theme_inc( $smart_slug_parts . '/index' );
                    
					// This is a normal page
					require $transaction_name;
				}else {
					$type = '404';
					
					// This page was not found
					header::http_status( 404 );
					
					// Stop from potential infinite loops
					if ( 1 == preg_match( '/images?/', $_SERVER['REQUEST_URI'] ) )
						exit;
					
                    // Set the transaction name
                    $transaction_name = theme_inc( '404' );
                    
					// The page they are looking for can't be found
					require $transaction_name;
				}
			break;
		}
	} else {
		// We know that this is a page or category, that's all we have to check
		define( 'SLUG', $slug_parts[0] );
		
		if ( 'css' == SLUG ) {
			$type = 'css';
            
            // Set the transaction name
			$transaction_name = inc( 'css' );
		} elseif ( 'js' == SLUG ) {
			$type = 'js';
            
			// Set the transaction name
			$transaction_name = inc( 'javascript' );
		} elseif ( 'js2' == SLUG ) {
			$type = 'js';
			
			// Set the transaction name
			$transaction_name = inc( 'direct-javascript' );
		} elseif ( is_file( THEME_PATH . SLUG . '.php' ) ) {
			$type = 'page';
			
            // Set the transaction name
            $transaction_name = theme_inc( SLUG );
            
			// This is a normal page
			require $transaction_name;
		} elseif ( is_file( THEME_PATH . SLUG . '/index.php' ) ) {
			$type = 'page';
			
            // Set the transaction name
            $transaction_name = theme_inc( SLUG . '/index' );
            
			// This is a normal page
			require $transaction_name;
		} else {
			$type = '404';
			
			// This page was not found
			header::http_status( 404 );
			
			// Stop from potential infinite loops
			if ( 1 == preg_match( '/images?/', $_SERVER['REQUEST_URI'] ) )
				exit;
			
			// Set the transaction name
            $transaction_name = theme_inc( '404' );
            
            // The page they are looking for can't be found
            require $transaction_name;
		}
	}
}

if ( extension_loaded( 'newrelic' ) )
    newrelic_name_transaction( str_replace( ABS_PATH, '', $transaction_name ) );