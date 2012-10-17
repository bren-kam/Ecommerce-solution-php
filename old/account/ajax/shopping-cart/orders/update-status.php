<?php
/**
 * @page Update Order Status
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'update-order-status' );
$ajax->ok( $user, _('You must be signed in to modify orders.') );

// Instantiate class
$s = new Shopping_Cart;

// Delete user
$ajax->ok( $s->update_order_status( $_POST['woid'], $_POST['s'] ), _('An error occurred while trying to change your order status.  Refresh the page and try again.') );

// Send response
$ajax->respond();