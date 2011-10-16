<?php
/**
 * @page Custom Products - Autocomplete
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'custom-products-autocomplete' );
$ajax->ok( $user, _('You must be signed in to autocomplete products.') );

// Get the right suggestions for the right type
switch( $_POST['type'] ) {
	case 'brand':
		$b = new Brands;
		
		$ac_suggestions = $b->autocomplete_custom( $_POST['term'] );
	break;
	
	case 'product':
		// Instantiate Class
		$p = new Products;
		
		$ac_suggestions = $p->autocomplete_custom( $_POST['term'], 'name' );
	break;

	case 'sku':
		// Instantiate Class
		$p = new Products;
		
		$ac_suggestions = $p->autocomplete_custom( $_POST['term'], 'sku' );
	break;

	default: break;
}

// It needs to be empty if nothing else
$suggestions = array();

if( is_array( $ac_suggestions ) )
foreach( $ac_suggestions as $acs ) {
	$suggestions[] = array( 'name' => html_entity_decode( $acs['name'], ENT_QUOTES, 'UTF-8' ), 'value' => $acs['value'] );
}

// Sent by the autocompleter
$ajax->add_response( 'suggestions', $suggestions );

// Send the response
$ajax->respond();