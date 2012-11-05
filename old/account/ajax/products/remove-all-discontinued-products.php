<?php
/**
 * @page Remove All Discontinued Products
 * @package Grey Suit Retail
 */

 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'remove-all-discontinued-products' );
$ajax->ok( $user, _('You must be signed in to remove a product.') );

$p = new Products;

// Remove all the products
$ajax->ok( $p->remove_discontinued_products(), _('An error occurred while trying to remove your discontinued products. Please refresh the page and try again.') );

// Remove the product then lower the count
jQuery('#dProductList')->empty();

// Add the jQuery Response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();