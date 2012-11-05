<?php
/**
 * @page Add Shipping Method
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'edit-shipping-zip-codes' );
$ajax->ok( $user, _('You must be signed in to delete shipping methods.') );

global $user;

$s = new Shopping_Cart;

$data = $_POST;
$zips = array();

$keys = array_keys( $data );

// Extract shipping methods
foreach( $keys as $key ){
	if( stristr( $key, 'hZip' ) ) {
		$zips[] = str_ireplace( 'hZip', '', $key );
	}
}

$ajax->ok( $s->update_shipping_zip_codes( $_POST['hID'], $zips ), _('An error occurred while trying to update your shipping method zip codes. Please refresh the page and try again.') );

// jQuery( '#dShippingMethod' . $_GET['wsmid'] )->detach();
jQuery( '.close:first' )->click();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();