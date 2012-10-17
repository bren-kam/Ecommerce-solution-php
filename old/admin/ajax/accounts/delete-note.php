<?php
/**
 * @page Delete Note
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'delete-note' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a note.') ) );
		exit;
	}
	
	// Instantiate class
	$w = new Websites;
	
	// Delete note
	$result = $w->delete_note( $_POST['nid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete your note. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}