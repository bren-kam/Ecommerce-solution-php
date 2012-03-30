<?php
/**
 * @page Brands - Remove Brand
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'remove-brand' );
$ajax->ok( $user, _('You must be signed in to remove a brand.') );

$b = new Brands;

$ajax->ok( $b->remove( $_GET['bid'] ), _('An error occurred while trying to remove your brand. Please refresh the page and try again.') );

jQuery( '#dBrand_' . $_GET['bid'] )
	->remove()
	->updateBrandsSequence();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send the response
$ajax->respond();