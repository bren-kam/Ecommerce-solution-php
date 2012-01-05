<?php
/**
 * @page Upgrade Websites
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;

echo $w->upgrade_websites( ( isset( $_GET['live'] ) && 'true' == $_GET['live'] ) ? 1 : 0 );
