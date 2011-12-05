<?php
/**
 * @page Craigslist - Download Ads
 * @package Imagine Retailer
 */

header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . format::slug( $user['website']['title'] ) . '-' . 'craigslist-ads.csv"');

// Initialize class
$c = new Craigslist;
$w = new Websites;

// Get the location
$contact_us = $w->get_page_by_slug( 'contact-us' );
$addresses = unserialize( $w->get_pagemeta_by_key( $contact_us['website_page_id'], 'addresses' ) );
$location = ( is_array( $addresses ) ) ? $addresses[0]['city'] . ', ' . $addresses[0]['state'] : '';

// Get ads
$craigslist_ads = $c->download();

// Declare variables
$domain = ( empty( $user['website']['subdomain'] ) ) ? $user['website']['domain'] : $user['website']['subdomain'] . '.' . $user['website']['domain'];

// Set it so we can use fputcsv
$outstream = fopen("php://output", 'w');

// Set up the head section
fputcsv( $outstream, array('Header', 'Location', 'Ad', 'Category', 'Item Number') );

// Put the rest of the items
foreach ( $craigslist_ads as $cad ) {
    $ad = str_replace( '[Product Name]', $cad['name'], $cad['text'] );
    $ad = str_replace( '[Store Name]', $user['website']['title'], $ad );
    $ad = str_replace( '[Store Logo]', '<img src="http://' . $domain . '/custom/uploads/images/' . $user['website']['logo'] . '" alt="" />', $ad );
    $ad = str_replace( '[Category]', $cad['category'], $ad );
    $ad = str_replace( '[Brand]', $cad['brand'], $ad );
    $ad = str_replace( '[Product Description]', $cad['description'], $ad );
    $ad = str_replace( '[SKU]', $cad['sku'], $ad );
    $ad = str_replace( '[Photo]', '<img src="http://' . $cad['image'] . '" alt="" />', $ad );

    fputcsv( $outstream, array(
        $cad['title']
        , $location
        , $ad
        , $cad['top_category']
        , $cad['sku']
    ));
}