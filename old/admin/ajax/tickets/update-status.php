<?php
/**
 * @page Update Ticket's Status
 * @package Grey Suit Retail
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-ticket-status' ) ) {
	$t = new Tickets;
	
	$result = $t->update_status( $_POST['tid'], $_POST['s'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the ticket's status. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}