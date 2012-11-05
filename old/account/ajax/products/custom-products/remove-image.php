<?php
/**
 * @page Remove Image
 * @package Grey Suit Retail
 */
 
 // Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'remove-image' );
$ajax->ok( $user, _('You must be signed in to remove a product image.') );

// Instantiate class
$p = new Products;
$f = new Files;

// Type Juggling
$product_id = (int) $_POST['pid'];

// Make sure it's a valid custom product
$ajax->ok( $p->get_custom_product( $product_id ), _('You do not have permission to remove a product image from this product') );

// Declare variables
$industry = $p->get_industry( $product_id );
$image = basename( $_POST['i'] );

// Delete images from amazon S3
$f->delete_image( "products/$product_id/$image", $industry );
$f->delete_image( "products/$product_id/thumbnail/$image", $industry );
$f->delete_image( "products/$product_id/thumbnail/small/$image", $industry );
$f->delete_image( "products/$product_id/thumbnail/large/$image", $industry );

// Delete image from database
$ajax->ok( $p->remove_image( $image, $product_id ), _('An error occurred while trying to remove your product image. Please refresh the page and try again.') );

// Send response
$ajax->respond();