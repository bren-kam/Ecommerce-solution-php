<?php
/**
 * @page List Visitor Details
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$a = new Analytics();
$dt = new Data_Table();

list( $start_date, $end_date ) = $a->dates();

// Set variables
$dt->order_by( 'a.`name`', 'page_visits', 'subscribed', 'date_visited' );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] . " AND a.`date_created` >= '$start_date' AND a.`date_created` <= '$end_date'" );
$dt->search( array( 'a.`name`' => false ) );

// Get visitors
$visitors = $a->list_visitors( $dt->get_variables() );
$dt->set_row_count( $a->count_visitors( $dt->get_where() ) );

// Initialize Variable
$data = array();

// Create output
if ( is_array( $visitors ) )
foreach ( $visitors as $v ) {
	$name = ( 'Anonymous' == $v['name'] ) ? 'Anonymous ' . $v['analytics_visitor_id'] : $v['name'];
	$data[] = array('<a href="/analytics/visitor-details/?avid=' . $v['analytics_visitor_id'] . '" title="' . $name . '">' . $name . '</a>',
					number_format( $v['page_visits'] ),
					( $v['subscribed'] ) ? 'Yes' : 'No',
					$v['date_visited']
				);
}

// Send response
echo $dt->get_response( $data );