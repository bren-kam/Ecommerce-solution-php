<?php
/**
 * @page Delete Coupon
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-coupon' );
$ajax->ok( $user, _('You must be signed in to delete a coupon.') );

// Instantiate class
$c = new Coupons;

// Delete user
$ajax->ok( $c->delete( $_GET['wcid'] ), _('An error occurred while trying to delete your coupon. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();