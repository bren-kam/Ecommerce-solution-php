<?php
/**
 * @page Block Product
 * @package Grey Suit Retail
 */

 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'block-product' );
$ajax->ok( $user, _('You must be signed in to block a product.') );

$p = new Products;
$product = $p->get_product( $_GET['pid'] );

// Delete the product
$ajax->ok( $p->block_products( $product['sku'] ), _('An error occurred while trying to block your product. Please refresh the page and try again.') );

// Remove the product then lower the count
jQuery('#dProduct_' . $_GET['pid'])
	->remove()
	->lowerProductCount();

// Add the jQuery Response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();