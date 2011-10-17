<?php
/**
 * @page Search
 * @package Imagine Retailer
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user ) {
	echo json_encode( array( 'result' => false, 'error' => _('You have been logged out. Please sign in again to continue.'), 'redirect' => 'true' ) );
	return false;
}

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'search' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in search websites.') ) );
		exit;
	}
	
	$_SESSION['websites']['search'] = $_POST['s'];
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'error' => _('An error occurred while trying to search the websites. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}