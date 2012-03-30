<?php
/**
 * @page Edit Website
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure they have permission to remove it
if ( 10 == $user['role'] ) {
    $w = new Websites;
    $w->delete( $_GET['wid'] );
}

// Redirect to main website page
url::redirect( '/accounts/' );
?>