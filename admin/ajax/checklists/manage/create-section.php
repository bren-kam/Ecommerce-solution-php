<?php
/**
 * @page Create Section
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( nonce::verify( $_POST['_nonce'], 'create-section' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to create a section.') ) );
		exit;
	}
	
	// Instantiate class
	$c = new Checklists;
	
	// Create section
	$checklist_section_id = $c->create_section();
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $checklist_section_id, 'error' => _('An error occurred while trying to create your section. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}