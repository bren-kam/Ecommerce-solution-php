<?php
/**
 * @page Get Product Dialog Info
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'get-product-dialog-info' );
$ajax->ok( $user, _('You must be signed in to get product information.') );

// Instantiate class
$p = new Products;

$ajax->add_response( 'product', $p->get_complete_website_product( $_POST['pid'] ) );
$ajax->add_response( 'product_options', $p->brand_product_options( $_POST['pid'] ) );

// Send response
$ajax->respond();