<?php
/**
 * @page Remove Attachment
 * @package Imagine Retailer
 */

if( nonce::verify( $_POST['_nonce'], 'remove-attachment' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to remove an attachment.') ) );
		exit;
	}

	// Instantiate class
	$f = new Files;
	
	// Remove the upload
	$result = $f->remove_upload( $_POST['tuid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to remove your attachment. Please refresh the pay and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}