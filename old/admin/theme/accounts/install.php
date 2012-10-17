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

if ( empty( $_GET['wid'] ) )
	url::redirect( $_SERVER['HTTP_REFERER'] );

$w = new Websites;

$w->install( $_GET['wid'] );

url::redirect( '/accounts/edit/?wid=' . $_GET['wid'] );
?>