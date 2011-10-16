<?php
/**
 * @page Create Product
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if( nonce::verify( $_POST['_nonce'], 'create-product' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to create a product.') ) );
		exit;
	}
	
	$p = new Products;
	
	$result = $p->create( $user['user_id'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to create your product. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}