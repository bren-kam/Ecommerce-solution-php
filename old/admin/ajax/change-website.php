<?php
/**
 * @page Change Website
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'change-website' );
$ajax->ok( $user, _('You must be signed in change a website.') );

// Set the website_id
$website_id = (int) $_GET['wid'];

// Set the website
if ( array_key_exists( $website_id, $user['websites'] ) ) {
	// Set the website
	$user['website'] = $user['websites'][$website_id];
	
	// Set the cookie
	set_cookie( 'wid', $website_id, 172800 ); // 2 days
	
	// We need to refresh the page
	$ajax->add_response( 'refresh', '1' );
}

// Send the response
$ajax->respond();