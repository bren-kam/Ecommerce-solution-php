<?php
/**
 * @page Get Random Headline
 * @package Imagine Retailer
 * @subpackage Account
 */
$ajax = new AJAX( $_GET['_nonce'], 'ramdom-headline' );
$ajax->ok( $user, _('You must be signed in to get a random title.') );

// Instantiate Class
$c = new Craigslist;

$random_headline = $c->get_random_headline( $_GET['cid'] );

jQuery("#tTitle")->val( $random_headline );

$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();