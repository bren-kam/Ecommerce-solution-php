<?php
/**
 * @page Update an Issue's Status
 * @package Imagine Retailer
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-issue-status' ) ) {
	$i = new Issues;
	
	$result = $i->update_status( $_POST['ik'], $_POST['s'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update your issue's status. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}