<?php
/**
 * @page Create Category
 * @package Real Statistics
 * @subpackage Admin
 */
 
if( nonce::verify( $_POST['_nonce'], 'create-category' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to create a category') ) );
		exit;
	}
	
	$c = new Categories;
	
	$result = $c->create( $_POST['sParentCategory'], $_POST['tName'], $_POST['tSlug'], $_POST['hAttributes'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to create category. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}