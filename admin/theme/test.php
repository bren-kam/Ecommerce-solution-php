<?php
$a = new Analytics;

// Get the date from today
//$date = new DateTime();
//$date->sub( new DateInterval('P0D') );

//$a->add_craigslist_stats( $date->format( 'Y-m-d' ) );

library('gsr-api-feed');
$api = new GSR_API_Feed( 'd29ef52d65d77ce46db77d010f37e41e' );
fn::info( $api->get_product_groups() );
