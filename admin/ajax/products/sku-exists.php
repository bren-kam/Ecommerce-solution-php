<?php
/**
 * @page SKU Exists
 * @package Grey Suit Retail
 */

 // Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'sku-exists' );
$ajax->ok( $user, _('You must be signed in to add a request.') );

$p = new Products;

$product = $p->sku_exists( $_POST['sku'] );

if ( $product ) {
	// Needs to be not be owned
	$ajax->ok( '0' == $product['owned'], _('A product with same SKU already exists in record and it is already added in your website.') );
	
	unset( $product['owned'] );
	
	// Give them the product
	$ajax->add_response( 'product', $product );
	$ajax->add_response( 'confirm', _('A product with same SKU already exists in record. Do you want to add into your product list?') );
} else {
	// No product
	$ajax->add_response( 'product', false );
}

// Send response
$ajax->respond();