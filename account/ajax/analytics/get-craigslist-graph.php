<?php
/**
 * @page Get Graph
 * @package Imagine Retailer
 * @subpackage Analytics
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'get-craigslist-graph' );
$ajax->ok( $user, _('You must be signed in to get a graph.') );

// Instantiate classes
$a = new Analytics( NULL );

$craigslist_market_id = (int) $_POST['cmid'];
$object_id = (int) $_POST['oid'];

$records = $a->get_craigslist_metric_by_date( $_POST['t'], $_POST['metric'], $craigslist_market_id, $object_id );

foreach ( $records as $r_date => $r_value ) {
	$plotting_array[] = array( $r_date, $r_value );
}

echo json_encode( $plotting_array );