<?php
/**
 * @page Add Brand
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'add-brand' );
$ajax->ok( $user, _('You must be signed in to add a brand.') );

$b = new Brands;
$brand = $b->add_top_brand( $_POST['bid'], $_POST['s'] );

if( $brand ) {
	$dBrand = '<div id="dBrand_' . $_POST['bid'] . '" class="brand">';
	$dBrand .= '<img src="' . $brand['image'] . '" title="' . $brand['name'] . '" />';
	$dBrand .= '<h4>' . $brand['name'] . '</h4>';
	$dBrand .= '<p class="brand-url"><a href="' . $brand['link'] . '" title="' . $brand['name'] . '" target="_blank" >' . $brand['link'] . '</a></p>';
	$dBrand .= '<a href="/ajax/products/brands/remove/?_nonce=' . nonce::create('remove-brand') . '&amp;bid=' . $_POST['bid'] . '" title="' . _('Remove') . '" class="remove" ajax="1">' . _('Remove') . '</a>';
	$dBrand .= '</div>';
	
	jQuery('#brands')
		->append( $dBrand )
		->sparrow();

	// Add a response
	$ajax->add_response( 'jquery', jQuery::getResponse() );
}

// Send the response
$ajax->respond();