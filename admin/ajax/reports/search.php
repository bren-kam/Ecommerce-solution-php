<?php
/**
 * @page Search
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'search' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in get a report.') ) );
		exit;
	}
	
	$w = new Websites;
	
	$websites = $w->report( $_POST['c'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'report' => $websites, 'error' => _('An error occurred while trying to get your report. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}