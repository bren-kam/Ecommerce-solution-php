<?php
/**
 * @page Autocomplete Websites
 * @package Imagine Retailer
 * @subpackage Admin
 */

if( nonce::verify( $_POST['_nonce'], 'autocomplete' ) ) {
	// Get the right suggestions for the right type
	switch( $_POST['type'] ) {
		case 'domain':
			$w = new Websites;

			$results = $w->autocomplete( $_POST['term'], 'domain' );
		break;
		
		case 'store_name':
			$u = new Users;

			$results = $u->autocomplete( $_POST['term'] , 'store_name' );
			
			foreach( $results as &$result ) $result['store_name'] = stripslashes( $result['store_name'] );
		break;
		
		case 'title':
			$w = new Websites;
			
			$results = $w->autocomplete( $_POST['term'], 'title' );
			foreach( $results as &$result ) $result['title'] = stripslashes( $result['title'] );
		break;
	}
	
// Needs to be in JSON
	echo json_encode( array( 'objects' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}