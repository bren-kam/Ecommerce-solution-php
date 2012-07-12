<?php
/**
 * @page Test Data Tables
 * @package Grey Suit Retail
 */

// Instantiate classes
$dt = new Data_Table();

// Set the row count
$dt->set_row_count( 5 );

$data = array(
	array( '1/1', '1/2', '1/3', '1/4', '1/5' ),
	array( '2/1', '2/2', '2/3', '2/4', '2/5' ),
	array( '3/1', '3/2', '3/3', '3/4', '3/5' ),
	array( '4/1', '4/2', '4/3', '4/4', '4/5' ),
	array( '5/1', '5/2', '5/3', '5/4', '15/5' )
);

// Send response
echo $dt->get_response( $data );