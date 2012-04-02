<?php
/**
 * @page Brands - Autocomplete
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'brands-autocomplete' );
$ajax->ok( $user, _('You must be signed in to autocomplete brands.') );

$b = new Brands;

$ac_suggestions = $b->autocomplete( $_POST['term'] );

if ( is_array( $ac_suggestions ) )
foreach ( $ac_suggestions as $acs ) {
	$suggestions[] = array( 'name' => html_entity_decode( $acs['name'], ENT_QUOTES, 'UTF-8' ), 'value' => $acs['value'] );
}

// Sent by the autocompleter
$ajax->add_response( 'suggestions', $suggestions );

// Send the response
$ajax->respond();