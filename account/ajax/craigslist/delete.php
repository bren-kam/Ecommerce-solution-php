<?php
/**
 * @page Delete Email List
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-craigslist-ad' );
$ajax->ok( $user, _('You must be signed in to delete a craigslist ad.') );

// Instantiate class
$c = new Craigslist;

// Delete user
$ajax->ok( $c->delete( $_GET['caid'] ), _('An error occurred while trying to delete your Craigslist Ad. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();