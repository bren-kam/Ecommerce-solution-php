<?php
/**
 * @page Set Product
 * @package Imagine Retailer
 * @subpackage Account
 */

if ( !nonce::verify( $_POST['nonce'], 'craigslist' ) ) return false;

// Instantiate classes
$c = new Craigslist;
$category_id = $_POST['category_id'];

$result = $c->count_templates_for_category( $category_id );

if ( !$result ){
	echo json_encode( array( 'noresults' => true, 'result' => 0 ) );
} else {
	echo json_encode( array( 'noresults' => false, 'result' => $result ) );
}
