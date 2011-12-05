<?php
/**
 * @page Set Product
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$c = new Craigslist;

$result = $c->count_templates_for_category( $_POST['cid'] );

if ( !$result ){
	echo json_encode( array( 'noresults' => true, 'result' => 0 ) );
} else {
	echo json_encode( array( 'noresults' => false, 'result' => $result ) );
}
