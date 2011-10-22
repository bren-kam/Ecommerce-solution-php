<?php
/**
 * @page Update Date Due
 * @package Imagine Retailer
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-date-due' ) ) {
	$t = new Tickets;
	
	// Get the date pieces
	$date = explode( '-', $_POST['d'] );
	
	// Update the date
	$result = $t->update_date_due( $_POST['tid'], $date[2] . '-' . $date[0] . '-' . $date[1] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the ticket's due date. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}