<?php
/*
$array = json_decode( curl::get( 'http://www.siteontime.com/bigsandydata.php' ) );

foreach ( $array as $a ) {
    fn::info( $a );
    exit;
}
*/
library('ashley-api/ashley-api');
$a = new Ashley_API();
//$package_templates = $a->get_package_templates( 'Bedaa', 'B128' );
//fn::info( $package_templates );
fn::info( $a->get_item_features() );
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
