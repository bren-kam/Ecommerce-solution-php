<?php
/**
 * @page Edit Website
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

if( empty( $_GET['wid'] ) )
	url::redirect( $_SERVER['HTTP_REFERER'] );

$w = new Websites;

$w->delete( $_GET['wid'] );

url::redirect( '/websites/' );
?>