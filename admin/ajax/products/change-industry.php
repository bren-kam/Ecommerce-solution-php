<?php
/**
 * @page Change Industry
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'change-industry' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to change the industry of a product.') ) );
		exit;
	}
	
	$p = new Products;
	
	$result = $p->change_industry( $_POST['pid'], $_POST['iid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _('An error occurred while trying to change the industry on your product. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}