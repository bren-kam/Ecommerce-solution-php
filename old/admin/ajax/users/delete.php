<?php
/**
 * @page Delete Product
 * @package Real Statistics
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'delete-user' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a user.') ) );
		exit;
	}
	
	$result = $u->delete( $_POST['uid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete the user. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}