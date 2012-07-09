<?php

echo format::string_to_entity( ".com" );
exit;
/*
$array = json_decode( curl::get( 'http://www.siteontime.com/bigsandydata.php' ) );

echo count( $array );
foreach ( $array as $a ) {
    fn::info( $a );
    exit;
}

library('ashley-api/ashley-api');
$a = new Ashley_API();
$packages = $a->get_packages();
exit;

// Load the library
library( 'craigslist-api' );
$b = new Base_Class();
// Create API object
$craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );
$markets = $b->db->get_results( 'SELECT a.`craigslist_market_id`, a.`website_id`, a.`market_id`, b.`cl_market_id` FROM `craigslist_market_links` AS a LEFT JOIN `craigslist_markets` AS b ON ( a.`craigslist_market_id` = b.`craigslist_market_id` ) WHERE 1', ARRAY_A );

foreach ( $markets as $m ) {
    $cl_categories = $craigslist_api->get_cl_market_categories( $m['cl_market_id'] );

    if ( is_array( $cl_categories ) )
    foreach ( $cl_categories as $clc ) {
        if ( 'furniture' == $clc->name ) {
            $b->db->update( 'craigslist_market_links', array( 'cl_category_id' => $clc->cl_category_id ), array( 'website_id' => $m['website_id'], 'craigslist_market_id' => $m['craigslist_market_id'] ), 'i', 'ii' );
            break;
        }
    }
}


$s = new SiteOnTime();
$s->run();




// Declare classes
$a = new Analytics();
$c = new Craigslist;
$m = new Mobile_Marketing();

// Determine date range
$date = new DateTime();
$date->sub( new DateInterval('P2D') );

// Update the stats
$a->update_craigslist_stats( $date->format('Y-m-d') );

// Update the tags for analytics of products
$c->update_tags();
*/

$sot = new SiteOnTime();
$sot->run();
