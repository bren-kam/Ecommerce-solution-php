<?php
/**
 * @page Change State
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if( nonce::verify( $_POST['_nonce'], 'change-state' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in view websites.') ) );
		exit;
	}
	
	switch( $_POST['s'] ) {
		default:
		case 'all':
			unset( $_SESSION['websites']['state'] );
		break;
		
		case 'live':
			$_SESSION['websites']['state'] = 1;
		break;
		
		case 'staging':
			$_SESSION['websites']['state'] = 0;
		break;
	}
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _('An error occurred while trying to change the state of the websites. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}