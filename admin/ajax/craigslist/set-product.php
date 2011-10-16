<?php
/**
 * @page Set Product
 * @package Imagine Retailer
 * @subpackage Account
 */

if( !nonce::verify( $_POST['nonce'], 'craigslist' ) ) return false;

// Instantiate classes
$c = new Craigslist;
$query = $_POST['query'];
$website_id = $user['website']['website_id'];


if( !$product_id = $c->get_product_id( $_POST['search_by'], $query ) ) echo json_encode( array( 'result' => false, 'product_id' => false, 'message' => 'Failed to get product id.  Please refresh the page and try again.' ) );

$product_result = $c->get_product( $product_id );
$image_result = $c->get_product_image_urls( $product_id );
$website_info = $c->get_website_info( $website_id );

foreach( $website_info as &$info ){
	$info = stripslashes( $info );
}

if ( !( $product_result ) ) {
		echo json_encode( array( 'success' => false, 'message' => 'There was an error retrieving that product.' ) );
} else {
	echo json_encode( array(
	  'success' => true,
	  'product_description' => htmlspecialchars_decode( $product_result['description'] ), 
	  'product_id' => $product_result['product_id'],
	  'product_name' => $product_result['product_name'],
	  'category_name' => $product_result['category_name'],
	  'category_id' => $product_result['category_id'],
	  'sku' => $product_result['sku'],
	  'product_specs' => $product_result['product_specifications'],
	  'brand' => $product_result['brand'],
	  'store_name' => $website_info['website_name'],
	  'store_url' => $website_info['domain'],
	  'store_logo' => "<p align='center'><a href='http://" . $website_info['domain'] . "' title='" . $website_name . " Logo' target='_blank'><img src='http://" . $website_info['domain'] . "/custom/uploads/images/" . $website_info['logo'] . "' /></a></p>",
	  'images' => ( ( $image_result ) ? $image_result : '' )
	) );
}