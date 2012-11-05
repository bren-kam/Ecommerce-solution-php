<?php
/**
 * @page Get Notes
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'get-notes' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to get notes.') ) );
		exit;
	}
	
	// Instantiate class
	$c = new Checklists;
	
	// Update Note
	$notes = $c->get_notes( (int) $_POST['iid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'notes' => $notes, 'error' => _('An error occurred while trying to get your notes. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}