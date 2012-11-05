<?php
global $user;

// Not anyone can get here
if ( !$user )
	url::redirect( '/' );

// Set the website_id
$website_id = (int) $_GET['wid'];

// Set the website
if ( array_key_exists( $website_id, $user['websites'] ) ) {
	// Set the website
	$user['website'] = $user['websites'][$website_id];

	// Set the cookie
	set_cookie( 'wid', $website_id, 172800 ); // 2 days
}

url::redirect( '/' );
?>