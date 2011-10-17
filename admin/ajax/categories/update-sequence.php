<?php
/**
 * @page Update Category Sequence
 * @package Real Statistics
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-category-sequence' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to update category sequences') ) );
		exit;
	}
	
	$c = new Categories;
	
	$result = $c->update_sequence( $_POST['pcid'], explode( '&cat[]=', substr( $_POST['sequence'], 6 ) ) );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to update category sequences. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}