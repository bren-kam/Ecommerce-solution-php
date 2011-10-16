<?php
/**
 * @page Add Shipping Method
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-shipping-method' );
$ajax->ok( $user, _('You must be signed in to delete shipping methods.') );

global $user;

$s = new Shopping_Cart;

$ajax->ok( $s->delete_shipping_method( $_GET['wsmid'] ), _('An error occurred while trying to update your shipping method. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();