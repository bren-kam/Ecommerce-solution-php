<?php
/**
 * @page Update Ticket Assigned To
 * @package Imagine Retailer
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-assigned-to' ) ) {
	$t = new Tickets;
	
	$result = $t->update_assigned_to( $_POST['tid'], $_POST['atui'] );
	$tb = $t->get( $_POST['tid'] );
	
	$priorities = array( 
		0 => 'Normal',
		1 => 'High',
		2 => 'Urgent'
	);
	
	$new_user = $u->get_user( (int) $_POST['atui'] );
	
	$message = 'Hello ' . $new_user['first_name'] . ",\n\n";
	$message .= 'You have been assigned Ticket #' . $_POST['tid'] . ". To view it, follow the link below:\n\n";
	$message .= 'http://admin.' . DOMAIN . '/tickets/ticket/?tid=' . $_POST['tid'] . "\n\n";
	$message .= 'Priority: ' . $priorities[$tb['priority']] . "\n\n";
	$message .= "Sincerely,\n" . TITLE . " Team";
	
	fn::mail( $new_user['email'], 'You have been assigned Ticket #' . $_POST['tid'] . ' (' . $priorities[$tb['priority']] . ') - ' . $tb['summary'], $message, TITLE . ' <noreply@' . DOMAIN . '>' );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the ticket's assigned to. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}