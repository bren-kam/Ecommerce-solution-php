<?php
//$a = new Analytics;

// Get the date from today
//$date = new DateTime();
//$date->sub( new DateInterval('P0D') );

//$a->add_craigslist_stats( $date->format( 'Y-m-d' ) );

library('feed-api');
$api = new Feed_API( 'd29ef52d65d77ce46db77d010f37e41e' );
fn::info( $api->get_products( '2010-01-01', '2010-02-01', 0, 10 ) );
echo $api->raw_response();