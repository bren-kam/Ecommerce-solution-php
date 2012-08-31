<?php
//$a = new Ashley();
//$a->load_packages();

library('ashley-api/ashley-api');
$a = new Ashley_API();
fn::info( $a->get_items( array( 'LoadAllItemCategories', 'LoadCatalogSearchResults' ) ) );
/*$packages = $a->get_packages();

$i = 0;
foreach ( $packages as $package ) {
    $i++;
    if ( $i < 2000 )
        continue;
    fn::info( $package );exit;
}
exit;
/*
// Load the library
library( 'craigslist-api' );
$b = new Base_Class();
// Create API object
$craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );
$markets = $b->db->get_results( 'SELECT `website_id`, `market_id` FROM `craigslist_market_links` WHERE `cl_category_id` = 0', ARRAY_A );

foreach ( $markets as $m ) {
    $cl_categories = $craigslist_api->get_cl_market_categories( $m['market_id'] );

    if ( is_array( $cl_categories ) )
    foreach ( $cl_categories as $clc ) {
        if ( 'furniture' == $clc->name ) {
            $b->db->update( 'craigslist_market_links', array( 'cl_category_id' => $clc->cl_category_id ), array( 'website_id' => $m['website_id'], 'market_id' => $m['market_id'] ), 'i', 'ii' );
            break;
        }
    }
}

*/
