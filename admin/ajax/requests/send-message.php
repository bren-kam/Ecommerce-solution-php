<?php
/**
 * @page Send Message
 * @package Real Statistics
 * @subpackage Graphs
 */

if( nonce::verify( $_POST['_send_message_nonce'], 'send-message' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to send a message.') ) );
		exit;
	}
	
	$r = new Requests;
	
	$result = $r->send_message( $_POST['hRequestID'], $_POST['taMessage'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to send your message. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}