<?php
/**
 * @page Delete Request
 * @package Real Statistics
 * @subpackage Graphs
 */
 
if( nonce::verify( $_POST['_nonce'], 'delete-request' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a request.') ) );
		exit;
	}
	
	$r = new Requests;
	
	$result = $r->delete( $_POST['rid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete the request. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}