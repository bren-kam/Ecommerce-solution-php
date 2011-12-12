<?php
/**
 * @page Set Product
 * @package Imagine Retailer
 * @subpackage Account
 */

$ajax = new AJAX( $_POST['_nonce'], 'set-product' );
$ajax->ok( $user, _('You must be signed in to set a craigslist product.') );

// Instantiate class
$p = new Products;

// Try to get the product
$ajax->ok( $product = $p->get_product( $_POST['pid'] ), _('Failed to get product. Please refresh the page and try again.') );
$ajax->ok( $images = $p->get_product_image_urls( $_POST['pid'] ), _('Failed to get product images. Please refresh the page and try again.') );

jQuery('#hProductDescription')->val( $product['description'] );
jQuery('#hProductName')->val( $product['name'] );
jQuery('#hProductCategoryID')->val( $product['category_id'] );
jQuery('#hProductID')->val( $product['product_id'] );
jQuery('#hProductCategoryName')->val( $product['category'] );
jQuery('#hProductSKU')->val( $product['sku'] );
jQuery('#hProductSpecs')->val( $product['product_specs'] );
jQuery('#hProductBrandName')->val( $product['brand'] );
jQuery('#hStoreName')->val( $user['website']['title'] ); 
jQuery('#hStoreLogo')->val( $user['website']['logo'] );
jQuery('#hStoreURL')->val( 'http://' . $user['website']['domain'] );

$image_html = '';

if ( is_array( $images ) )
foreach ( $images as $i ) {
	$image_html .= '<img class="hiddenImage" name="hiddenImage" src="' . $i . '" />';
}

jQuery('#dProductPhotos')
	->html( $image_html )
	->determineTemplate(); // Needs to determine template
			  
// Add response
$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();