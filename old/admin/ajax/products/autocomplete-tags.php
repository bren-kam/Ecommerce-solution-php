<?php
/**
 * @page Autocomplete Tags
 * @package Grey Suit Retail
 * @subpackage Admin
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'autocomplete-tags' ) ) {
	$t = new Tags;
	
	// Get the autocomplete entries
	$results = $t->autocomplete( 'product', $_POST['term'] );
	
	// Needs to be in JSON
	echo json_encode( array( 'objects' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}