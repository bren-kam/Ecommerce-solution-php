<?php
// Set it as a background job
newrelic_background_job();

$mysqli =  mysqli_connect( '199.204.138.78', 'imaginer_admin', 'rbDxn6kkj2e4', 'imaginer_system' );

// Update automatic statistics
require( '/home/develop4/public_html/includes/libraries/statistics-api.php' );
$stat = new Stat_API( '941cb213d6bbf2dd73c1214fad6321e6' );

// Get the total paid users
$result = $mysqli->query( 'SELECT COUNT(`website_id`) AS websites FROM `websites` AND `status` = 1' );
$row = $result->fetch_assoc();

$stat->add_graph_value( 7139, $row['websites'], date('Y-m-d') ); // GSR - Total Paid Users

$result = $mysqli->query( 'SELECT COUNT( `product_id` ) AS count FROM `products` WHERE ( `timestamp` > DATE_SUB( NOW(), INTERVAL 3 DAY ) AND `user_id_modified` = 353 ) OR ( `date_created` > DATE_SUB( NOW(), INTERVAL 3 DAY ) AND `user_id_created` = 353 )' );
$row = $result->fetch_assoc();

if ( 0 == $row['count'] ) {
	$headers = "From: Grey Suit Retail <noreply@greysuitretail.com>" . "\r\n" .
		"Reply-to: Grey Suit Retail <noreply@greysuitretail.com>" . "\r\n" .
		"X-Mailer: PHP/" . phpversion();

	mail( 'kerry@studio98.com, rafferty@studio98.com, david@greysuitretail.com', 'Ashley Feed - No Update', 'There has been no update in the Ashley Feed for 3 days. Please investigate.', $headers );
}

// Remove unnecessary analytics data
//$result = $mysqli->query( 'DELETE FROM `analytics_visitors` WHERE `date_created` < DATE_SUB( NOW(), INTERVAL 30 DAY )' );
//$result = $mysqli->query( 'DELETE FROM `analytics_visitor_pages` WHERE `date_visited` < DATE_SUB( NOW(), INTERVAL 30 DAY )' );

// Delete empty products (that were created by going to the page)
$result = $mysqli->query( "DELETE FROM `products` WHERE '' = `name` AND ( 0 = `brand_id` OR `brand_id` IS NULL ) AND ( 'public' = `publish_visibility` OR '' = `publish_visibility` )" );

// Close connetion
$mysqli->kill( $mysqli->thread_id );
$mysqli->close();