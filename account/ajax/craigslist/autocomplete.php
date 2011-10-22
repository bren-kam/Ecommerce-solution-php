<?php
/**
 * @page Autocomplete Craigslist Search
 * @package Imagine Retailer
 * @subpackage Account
 */

if ( !nonce::verify( $_GET['_nonce'], 'craigslist' ) ) return false;

// Instantiate classes
$c = new Craigslist;

$website_id = $user['website']['website_id'];
$query = $_GET['query'];
$result = array( 'query' => $_GET['query'] );

// Get the right suggestions for the right type
switch ( $_GET['type'] ) {
	case 'products':
		switch ( $c->autocomplete( $_GET['query'], 'name', $website_id ) as $product ) {
			$results[] = html_entity_decode( $product['name'], ENT_QUOTES, 'UTF-8' );
		}
	break;

	case 'sku':
		switch ( $c->autocomplete( $_GET['query'], 'sku', $website_id ) as $sku ) {
			$results[] = html_entity_decode( $sku['sku'], ENT_QUOTES, 'UTF-8' );
		}
	break;

	default: 
	break;
}

$result['suggestions'] = $results;

echo json_encode( $result );