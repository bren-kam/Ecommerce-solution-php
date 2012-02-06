<?php
/**
 * @page Unsubscribe subscribe
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'unsubscribe-subscriber' );
$ajax->ok( $user, _('You must be signed in to unsubscribe a subscriber.') );

// Instantiate class
$m = new Mobile_Marketing();
$ajax->ok( $m->unsubscribe( $_GET['msid'] ), _('An error occurred while trying to unsubscribe this subscriber.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();