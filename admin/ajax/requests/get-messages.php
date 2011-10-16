<?php
/**
 * @page Get Messages
 * @package Real Statistics
 * @subpackage Graphs
 */
 
if( nonce::verify( $_POST['_nonce'], 'get-messages' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to get request messages.') ) );
		exit;
	}
	
	$r = new Requests;
	
	$messages = $r->get_messages( $_POST['rid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'messages' => $messages, 'error' => _('An error occurred while trying to get request messages. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}