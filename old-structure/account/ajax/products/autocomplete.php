<?php
/**
 * @page Products - Autocomplete
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'products-autocomplete' );
$ajax->ok( $user, _('You must be signed in to autocomplete products.') );

// Get the right suggestions for the right type
switch ( $_POST['type'] ) {
	case 'brand':
		$b = new Brands;
		
		$ac_suggestions = ( '1' == $_POST['owned'] ) ? $b->autocomplete_owned( $_POST['term'] ) : $b->autocomplete( $_POST['term'] );
	break;
	
	case 'product':
		// Instantiate Class
		$p = new Products;
		
		$ac_suggestions = ( '1' == $_POST['owned'] ) ? $p->autocomplete_owned( $_POST['term'], 'name' ) : $p->autocomplete( $_POST['term'], 'name' );
	break;

	case 'sku':
		// Instantiate Class
		$p = new Products;
		
		$ac_suggestions = ( '1' == $_POST['owned'] ) ? $p->autocomplete_owned( $_POST['term'], 'sku' ) : $p->autocomplete( $_POST['term'], 'sku' );
	break;
	
	case 'sku-products':
		// Instantiate Class
		$p = new Products;
		
		if ( '1' == $_POST['owned'] )
			$ac_suggestions = $p->autocomplete_owned( $_POST['term'], array( 'name', 'sku' ) );
	break;

	default: break;
}

// It needs to be empty if nothing else
$suggestions = array();

if ( is_array( $ac_suggestions ) )
foreach ( $ac_suggestions as $acs ) {
	$suggestions[] = array( 'name' => html_entity_decode( $acs['name'], ENT_QUOTES, 'UTF-8' ), 'value' => $acs['value'] );
}

// Sent by the autocompleter
$ajax->add_response( 'suggestions', $suggestions );

// Send the response
$ajax->respond();