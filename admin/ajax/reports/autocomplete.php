<?php
/**
 * @page Autocomplete Reports
 * @package Imagine Retailer
 * @subpackage Admin
 */

if( nonce::verify( $_POST['_nonce'], 'autocomplete' ) ) {
	// Get the right suggestions for the right type
	switch( $_POST['type'] ) {
		case 'brand':
			$b = new Brands;

			$results = $b->autocomplete( $_POST['term'] );
		break;
		
		case 'online_specialist':
			$w = new Websites;

			$results = $w->autocomplete_online_specialists( $_POST['term'] );
		break;
		
		case 'company':
			$c = new Companies;
			
			$results = $c->autocomplete( $_POST['term'] );
		break;
	}
	
	// Needs to return an array, even if nothing was gotten
	if( !$results )
		$results = array();
	
	// Needs to be in JSON
	echo json_encode( array( 'objects' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}