<?php
/**
 * @page Update CHecklist
 * @package Grey Suit Retail
 * @subpackage Admin
 */

if ( nonce::verify( $_POST['_ajax_update_checklist'], 'update-checklist' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to update a checklist.') ) );
		exit;
	}

    if ( $user['role'] < 7 ) {
		echo json_encode( array( 'result' => false, 'error' => _('You do not have permission to update this checklist.') ) );
		exit;
	}

	// Instantiate class
	$c = new Checklists;

	// Complete the items
	$result = $c->complete_items( $_POST['hWebsiteID'], $_POST['hTicketID'], $_POST['sChecklistItems'] );

	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to update your checklist. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}