<?php
/**
 * @page Delete Category
 * @package Real Statistics
 * @subpackage Admin
 */
 
if( nonce::verify( $_POST['_nonce'], 'delete-category' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a category') ) );
		exit;
	}
	
	$c = new Categories;
	
	$result = $c->delete( $_POST['cid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete category. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}