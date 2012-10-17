<?php
/**
 * @page Bot - Individual Ashley Feed
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$website_id = ( isset( $_GET['wid'] ) ) ? $_GET['wid'] : NULL;

// Redirect to main accountpage
if ( !$user )
    url::redirect( '/accounts/' );

$a = new Ashley_Feed();
$a->run( $website_id );

if ( !$user )
    url::redirect( "/accounts/edit/?wid=$website_id" );