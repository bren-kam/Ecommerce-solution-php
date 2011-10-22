<?php
/**
 * @page Change Visibility
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'change-visibility' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in view products.') ) );
		exit;
	}
	
	switch ( $_POST['s'] ) {
		default:
			unset( $_SESSION['product']['visibility'] );
		break;
		
		case 'live':
			$_SESSION['product']['visibility'] = 1;
		break;
		
		case 'staging':
			$_SESSION['product']['visibility'] = 0;
		break;
	}
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _('An error occurred while trying to change the visibility of products. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}