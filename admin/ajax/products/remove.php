<?php
/**
 * @page Remove Product
 * @package Grey Suit Retail
 */

 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'remove-product' );
$ajax->ok( $user, _('You must be signed in to remove a product.') );

$p = new Products;
$w = new Websites;

// Get settings
$settings = $w->get_settings('limited-products');

// Make sure they have permission to remove this product
$ajax->ok( $user['role'] >= 6 || '1' != $settings['limited-products'], _('You do not have permission to remove this product') );

// Delete the product
$ajax->ok( $p->remove( $_GET['pid'] ), _('An error occurred while trying to remove your product. Please refresh the page and try again.') );

// Remove the product then lower the count
jQuery('#dProduct_' . $_GET['pid'])
	->remove()
	->lowerProductCount();

// Add the jQuery Response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();