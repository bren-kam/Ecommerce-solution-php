<?php
/**
 * @page Delete Email List
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'edit-shipping' );
$ajax->ok( $user, _('You must be signed in to edit shipping.') );

global $user;

$s = new Shopping_Cart;

$ajax->ok( $s->update_shipping_method( $_POST['tName'], $_POST['sMethod'], $_POST['tAmount'], $_POST['hID'] ), _('An error occurred while trying to update your shipping method. Please refresh the page and try again.') );

jQuery( '#dShippingMethod' . $_POST['hID'] . ' td:first a' )->html('<span>' . $_POST['tName'] . '</span>' );
jQuery( '#dShippingMethod' . $_POST['hID'] . ' td:nth-child(2)' )->html('<span>' . _( $_POST['sMethod'] ) . '</span>' );
jQuery( '#dShippingMethod' . $_POST['hID'] . ' td:nth-child(3)' )->html('<span>$' . number_format( $_POST['tAmount'], 2 ) . '</span>' );
jQuery('.title-bar>.close')->click();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();