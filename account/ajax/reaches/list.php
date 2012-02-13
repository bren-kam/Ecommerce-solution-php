<?php
/**
 * @page List Tickets
 * @package Imagine Retailer
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user ) {
	echo json_encode( array( 
		'redirect' => true,
		'sEcho' => intval( $_GET['sEcho'] ),
		'iTotalRecords' => 0,
		'iTotalDisplayRecords' => 0,
		'aaData' => array()
	) );
	return false;
}


// Instantiate classes
$r = new Reaches;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`date_updated`', 'a.`date_created`', 'a.`website_id`' );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`message`' => false ) );

$reaches = $r->list_reaches( $dt->get_variables() );
$dt->set_row_count( $r->count_reaches( $dt->get_where() ) );

/* set up the data */
$data = array();

foreach ( $reaches as $reach ) {
	$data[] = array(
		'<a href="/reaches/reach/?rid=' . $reach['website_reach_id'] . '">' .$reach['name'] . '</a>',
		$reach['website'],
		$reach['assigned_to'],
		$reach['date_created']
		
	);
	
}



echo $dt->get_response( $data ); 