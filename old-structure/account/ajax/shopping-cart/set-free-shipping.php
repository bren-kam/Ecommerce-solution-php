<?php
/**
 * @page Delete Email List
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'set-free-shipping' );
$ajax->ok( $user, _('You must be signed in to edit shipping methods.') );

global $user;

$w = new Websites;

$quantity = ( $_POST['c'] ) ? $_POST['q'] : 0;

$settings = $w->get_settings( array( 'free-shipping-quantity' ) );

if( !isset( $settings['free-shipping-quantity'] ) ) {
	$success = $w->create_setting( 'free-shipping-quantity', $quantity );
} else {
	$success = $w->update_settings( array( 'free-shipping-quantity' => $quantity ) );
}

$ajax->ok( $success, _('An error occurred while trying to change shipping options.  Refresh the page and try again.') );

echo json_encode( array( 'success' => $success ) );