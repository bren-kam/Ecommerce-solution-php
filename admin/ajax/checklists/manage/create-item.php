<?php
/**
 * @page Create Item
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( nonce::verify( $_POST['_nonce'], 'create-item' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to create an item.') ) );
		exit;
	}
	
	// Instantiate class
	$c = new Checklists;
	
	// Create section
	$checklist_item_id = $c->create_item( $_POST['sid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $checklist_item_id, 'error' => _('An error occurred while trying to create your item. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}