<?php
global $user;

if ( $user['role'] <= 5 ) {
	// Find out if we can be here
	$w = new Websites;
	
	// Check if they have limited products
	$settings = $w->get_settings('limited-products');
	
	// Make sure they can be here
	if ( '1' == $settings['limited-products'] )
		url::redirect('/products/');
}

$product_id = (int) $_GET['pid'];

if( empty( $product_id ) )
	url::redirect( '/products/custom-products/' );

$p = new Products;
$new_product_id = $p->clone_product( $product_id );

// Redirect to the new cloned product
url::redirect( '/products/custom-products/add-edit/?pid=' . $new_product_id );