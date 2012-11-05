<?php
/**
 * @page Delete Product
 * @package Grey Suit Retail
 */

 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-custom-product' );
$ajax->ok( $user, _('You must be signed in to delete a product.') );

$p = new Products;

// Delete the product
$ajax->ok( $p->delete( $_GET['pid'] ), _('An error occurred while trying to delete your product. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();