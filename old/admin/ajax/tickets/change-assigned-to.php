<?php
/**
 * @page Update Ticket Assigned To
 * @package Grey Suit Retail
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'change-assigned-to' ) ) {
	// Change the session value
	if ( empty( $_POST['auid'] ) ) {
		unset( $_SESSION['tickets']['assigned-to'] );
	} else {
		$_SESSION['tickets']['assigned-to'] = (int) $_POST['auid'];
	}
		
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _("An error occurred while trying to change to the Assigned To user. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}