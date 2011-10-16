<?php
/**
 * @page Delete User
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'shopping-cart' );
$ajax->ok( $user, _('You must be signed in to delete a user.') );

global $user;

// Instantiate class
$s = new Shopping_Cart;

// Delete user
$ajax->ok( $s->delete_user( $_GET['uid'], $user['website']['website_id']  ), _('An error occurred while trying to delete your craigslist ad. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();