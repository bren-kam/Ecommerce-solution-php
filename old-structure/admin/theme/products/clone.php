<?php
$product_id = (int) $_GET['pid'];

if ( empty( $product_id ) )
	url::redirect( '/products/' );

$p = new Products;
$new_product_id = $p->clone_product( $product_id );

// Redirect to the new cloned product
url::redirect( '/products/add-edit/?pid=' . $new_product_id );