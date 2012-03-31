<?php
/**
 * @page Add Note
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( nonce::verify( $_POST['_ajax_add_note'], 'add-note' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to add a note.') ) );
		exit;
	}
	
	// Instantiate class
	$c = new Checklists;
	
	// Add a note
	$note_id = $c->add_note( $_POST['hItemId'], $_POST['taNote'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $note_id, 'error' => _('An error occurred while trying to add your note. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}