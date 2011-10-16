<?php
/**
 * @page Search
 * @package Imagine Retailer
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if( !$user ) {
	echo json_encode( array( 'result' => false, 'error' => _('You have been logged out. Please sign in again to continue.'), 'redirect' => 'true' ) );
	return false;
}

if( nonce::verify( $_POST['_nonce'], 'search' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in search websites.') ) );
		exit;
	}
	
	$_SESSION['craigslist']['search'] = ( isset( $_POST['s'] ) ) ? $_POST['s'] : NULL;
	$_SESSION['craigslist']['category'] = ( isset( $_POST['t'] ) ) ? $_POST['t'] : NULL;
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _('An error occurred while trying to search the craigslist templates. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}