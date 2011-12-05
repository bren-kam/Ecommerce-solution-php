<?php
/**
 * @page Get Craigslist Template
 * @package Imagine Retailer
 * @subpackage Account
 */

$c = new Craigslist;

$cid = $_POST['craigslist_ad_id'];

$result = $c->get( $cid );

// Need to strip tags to display properly
if ( $result ) $result['text'] = htmlspecialchars_decode( $result['text'] );

echo json_encode( array( 'success' => true, 'results' => $result ) );