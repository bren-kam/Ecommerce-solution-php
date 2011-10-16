<?php
/**
 * @page Analytics - Email
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

// Redirect to main section if they don't have email marketing
if( !$user['website']['email_marketing'] )
	url::redirect('/analytics/');

$a = new Analytics();

echo $a->click_overlay_html( $_GET['mcid'] );