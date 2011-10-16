<?php
/**
 * @page Update sequence
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'update-website-product-sequence' );
$ajax->ok( $user, _('You must be signed in to update your product sequence.') );

// Instantiate class
$p = new Products;

// Determine the sequence and behold the awesomeness that is the next two lines
$sequence = explode( '&dProduct[]=', $_POST['s'] );
$sequence[0] = substr( $sequence[0], 11 );

// Make sure it updated successfully
$ajax->ok( $p->update_website_products_sequence( $sequence ), _('An error occurred while trying to update the sequence of your products. Please refresh the page and try again.') );

// Send response
$ajax->respond();