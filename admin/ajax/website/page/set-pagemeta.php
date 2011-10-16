<?php
/**
 * @page Sets pagemeta
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'set-pagemeta' );
$ajax->ok( $user, _('You must be signed in to set pagemeta.') );

// Instantiate class
$w = new Websites();

// Type Juggling
$website_page_id = (int) $_POST['wpid'];

// Need to make sure we get a proper key
switch( $_POST['k'] ) {
	case 'ham':
		$key = 'hide-all-maps';
	break;
	
	case 'mlm':
		$key = 'multiple-location-map';
	break;
	
	default:
		$ajax->ok( false, _('An error occurred when trying to change your setting. Please refresh the page and try again') );
	break;
}

// Set Pagemeta
$ajax->ok( $w->set_pagemeta( $website_page_id, array( $key => $_POST['v'] ) ), _('An error occurred while trying to save your setting. Please refresh the page and try again.') );

// Send response
$ajax->respond();