<?php
/**
 * @page Ashley Replacement
 * @package Grey Suit Retail
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'autocomplete' ) ) {
	$p = new Products;
	
	$suggestions = $p->autocomplete_new_ashley( $_POST['term'] );
	
	if ( !is_array( $suggestions ) )
		$suggestions = array();
	
	// Needs to be in JSON
	echo json_encode( array( 'suggestions' => $suggestions ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}