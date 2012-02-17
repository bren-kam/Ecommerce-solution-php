<?php
//$a = new Analytics;

// Get the date from today
//$date = new DateTime();
//$a->add_craigslist_stats( $date->format( 'Y-m-d' ) );

//$xml = new xml();
//
//$elements = array(
//    'toppings' => array(
//        'item' => 'pepperoni'
//    )
//);
//
//$elements = array(
//    'toppings' => array(
//        'items' => array(
//            'item' => array(
//                'pepperoni'
//                , 'tomato'
//            )
//        )
//    )
//);
//
//
//$xml->parse( $elements );
//
//echo $xml->output();

library('gsr-api-feed');
$api = new GSR_API_Feed( 'd29ef52d65d77ce46db77d010f37e41e' );

print_r( $api->get_feed() );
