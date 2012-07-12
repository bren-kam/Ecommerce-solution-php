<?php
/**
 * @page Update Priority
 * @package Grey Suit Retail
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-priority' ) ) {
	$t = new Tickets;
	
	$result = $t->update_priority( $_POST['tid'], $_POST['p'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the ticket's priority. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}