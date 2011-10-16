<?php
/**
 * @page Delete Email List
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'craigslist-ad' );
$ajax->ok( $user, _('You must be signed in to delete a craigslist ad.') );

// Instantiate class
$c = new Craigslist;

// Delete user
$ajax->ok( $c->delete( $_GET['cid'] ), _('An error occurred while trying to delete your craigslist ad. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();