<?php
/**
 * @page Set Product
 * @package Imagine Retailer
 * @subpackage Account
 */

$ajax = new AJAX( $_POST['_nonce'], 'load-product' );
$ajax->ok( $user, _('You must be signed in to load a craigslist product.') );

// Instantiate class
$p = new Products;

if ( isset( $_POST['pid'] ) ) {
    // Assign the Product ID
    $product_id = $_POST['pid'];
} else {
    // That means they passed a craigslist ad ID
    $c = new Craigslist;

    // Get the ad
    $ad = $c->get( $_POST['caid'] );

    // Assign the Product ID
    $product_id = $ad['product_id'];
}

// Try to get the product
$ajax->ok( $product = $p->get_product( $product_id ), _('Failed to get product. Please refresh the page and try again.') );

// Get the website_product for the price
$website_product = $p->get_website_product( $product_id );

// Set price
$price = ( $website_product ) ? $website_product['price'] : 0;

// Try to get the images for the product
$ajax->ok( $images = $p->get_product_image_urls( $product_id ), _('Failed to get product images. Please refresh the page and try again.') );

jQuery('#hProductDescription')->val( $product['description'] );
jQuery('#hProductName')->val( $product['name'] );
jQuery('#hProductCategoryID')->val( $product['category_id'] );
jQuery('#aRandomHeadline')->attr( 'href', '/ajax/craigslist/get-random-headline/?_nonce=' . nonce::create('random-headline') . '&cid=' . $product['category_id'] );
jQuery('#hProductID')->val( $product['product_id'] );
jQuery('#hProductCategoryName')->val( $product['category'] );
jQuery('#hProductSKU')->val( $product['sku'] );
jQuery('#hProductBrandName')->val( $product['brand'] );
jQuery('#tPrice')->val( $price );


$image_html = '';

if ( is_array( $images ) )
foreach ( $images as $i ) {
	$image_html .= '<img class="hiddenImage" name="hiddenImage" src="' . $i . '" />';
}

jQuery('#dProductPhotos')
	->html( $image_html )
	->openEditorAndPreview(); // Needs to determine template
			  
// Add response
$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();