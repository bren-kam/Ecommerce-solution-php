<?php
/**
 * @page Change State
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'change-state' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in view accounts.') ) );
		exit;
	}
	
	switch ( $_POST['s'] ) {
		default:
		case 'all':
			unset( $_SESSION['accounts']['state'] );
		break;
		
		case 'live':
			$_SESSION['accounts']['state'] = 1;
		break;
		
		case 'staging':
			$_SESSION['accounts']['state'] = 0;
		break;
	}
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _('An error occurred while trying to change the state of the accounts. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}