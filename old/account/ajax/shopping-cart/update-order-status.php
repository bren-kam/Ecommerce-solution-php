<?php
/**
 * @page Delete Email List
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['nonce'], 'update-order-status' );
$ajax->ok( $user, _('You must be signed in to modify orders.') );

global $user;

// Instantiate class
$s = new Shopping_Cart;

// Delete user
$ajax->ok( $s->update_order_status( $_POST['website_order_id'], $_POST['status'] ), _('An error occurred while trying to change your order status.  Refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();