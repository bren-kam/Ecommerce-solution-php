<?php
/**
 * @page Get Graph
 * @package Grey Suit Retail
 * @subpackage Analytics
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'get-graph' );
$ajax->ok( $user, _('You must be signed in to get a graph.') );

// Instantiate classes
$a = new Analytics( $user['website']['ga_profile_id'] );

$records = $a->get_metric_by_date( $_POST['metric'] );

foreach ( $records as $r_date => $r_value ) {
	$plotting_array[] = array( $r_date, $r_value );
}

echo json_encode( $plotting_array );