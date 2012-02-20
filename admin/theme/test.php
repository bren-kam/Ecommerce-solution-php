<?php
//$a = new Analytics;

// Get the date from today
//$date = new DateTime();
//$date->sub( new DateInterval('P0D') );

//$a->add_craigslist_stats( $date->format( 'Y-m-d' ) );

library('craigslist-api');

$craigslist = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );

$c = new Categories();
$craigs = new Craigslist();
$categories = $c->get_all();

$new_tags = array();

if ( is_array( $categories ) )
foreach ( $categories as $category ) {
    $new_tags[$category['category_id']] = array( 'type' => 'category', 'name' => $category['name'] );
}

$tags = $craigslist->add_tags( $new_tags );

fn::info( $tags );

$craigs->add_tags( $tags );




//library('feed-api');
//$api = new Feed_API( 'd29ef52d65d77ce46db77d010f37e41e' );
//fn::info( $api->get_products( '2010-01-01', '2010-02-01', 0, 10 ) );
//echo $api->raw_response();