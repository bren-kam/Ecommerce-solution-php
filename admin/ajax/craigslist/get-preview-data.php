<?php
/**
 * @page Get_Preview_Data
 * @package Imagine Retailer
 * @subpackage Admin
 */

if( nonce::verify( $_POST['_nonce'], 'preview-craigslist' ) ) {
	$c = new Craigslist;
		
	$results = $c->get_preview_data( $_POST['cid'], $_POST['pid'] );
	
	
	// Needs to be in JSON
	echo json_encode( array( 'result' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}