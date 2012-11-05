<?php
/**
 * @page Update sequence
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'update-website-product-sequence' );
$ajax->ok( $user, _('You must be signed in to update your product sequence.') );

// Instantiate class
$p = new Products;

// Determine the sequence and behold the awesomeness that is the next two lines
$sequence = explode( '&dProduct[]=', $_POST['s'] );
$sequence[0] = substr( $sequence[0], 11 );

// Adjust them if it's not the first page
if ( '1' != $_POST['p'] ) {
	$increment = ( (int) $_POST['p'] - 1 ) * $_POST['pp'];
	
	foreach ( $sequence as $index => $product_id ) {
		$new_sequence[$index + $increment] = $product_id;
	}
	
	$sequence = $new_sequence;
}

// Make sure it updated successfully
$ajax->ok( $p->update_website_products_sequence( $sequence ), _('An error occurred while trying to update the sequence of your products. Please refresh the page and try again.') );

// Send response
$ajax->respond();