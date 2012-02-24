<?php
/**
 * @page Update Ticket Status
 * @package Imagine Retailer
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'change-status' ) ) {
	// Change the session value
	if ( empty( $_POST['s'] ) ) {
		$_SESSION['tickets']['status'] = '0';
	} else {
		$_SESSION['tickets']['status'] = (int) $_POST['s'];
	}
		
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _("An error occurred while trying to change to the status you were trying to view. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}