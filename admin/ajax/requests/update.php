<?php
/**
 * @page Update
 * @package Real Statistics
 * @subpackage Graphs
 */
 
if( nonce::verify( $_POST['_nonce'], 'update-request' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to approve or disapprove a request.') ) );
		exit;
	}
	
	$r = new Requests;
	
	if( 'approve' == $_POST['hAction'] ) {
		$result = $r->approve( (int) $_POST['hRequestID'] );
	} else {
		$result = $r->disapprove( (int) $_POST['hRequestID'] );
	}
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to update a request. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}