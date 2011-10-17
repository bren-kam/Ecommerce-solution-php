<?php
/**
 * @page Autocomplete Websites
 * @package Imagine Retailer
 * @subpackage Admin
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'autocomplete' ) ) {
	// Get the right suggestions for the right type		
	switch ( $_POST['type'] ) {
		case 'title':
			$c = new Craigslist;
			$results = $c->autocomplete( $_POST['term'], 'a.`title`' );
		break;
		
		case 'content':
			$c = new Craigslist;
			$results = $c->autocomplete( $_POST['term'], 'a.`description`' );
		break;
		
		case 'category':
			$c = new Craigslist;
			$results = $c->autocomplete( $_POST['term'], 'b.`name`' );
		break;
	}
	
	// Needs to be in JSON
	echo json_encode( array( 'objects' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}