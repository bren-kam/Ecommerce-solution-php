<?php
$mysqli =  mysqli_connect( 'localhost', 'imaginer_admin', 'rbDxn6kkj2e4', 'imaginer_system' ); 

// Update automatic statistics
require( '/home/imaginer/public_html/includes/libraries/statistics-api.php' );
$stat = new Stat_API( '941cb213d6bbf2dd73c1214fad6321e6' );

// Get the total paid users
$result = $mysqli->query( 'SELECT COUNT(`website_id`) AS websites FROM `websites`' );
$row = $result->fetch_assoc();

$stat->add_graph_value( 7139, $row['websites'], date('Y-m-d') ); // RS - Total Paid Users

// Close connetion
$mysqli->kill( $mysqli->thread_id );
$mysqli->close();