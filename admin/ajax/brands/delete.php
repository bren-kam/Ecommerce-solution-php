<?php
/**
 * @page Delete Brand
 * @package Real Statistics
 * @subpackage Admin
 */
 
if( nonce::verify( $_POST['_nonce'], 'delete-brand' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a brand') ) );
		exit;
	}
	
	$b = new Brands;
	
	$result = $b->delete( $_POST['bid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete brand. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}