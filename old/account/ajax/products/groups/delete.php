<?php
/**
 * @page Delete Product Group
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-product-group' );
$ajax->ok( $user, _('You must be signed in to delete a product group.') );

// Instantiate class
$pg = new Product_Groups;

// Delete product group
$ajax->ok( $pg->delete( $_GET['wpgid'] ), _('An error occurred while trying to delete your product group. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();