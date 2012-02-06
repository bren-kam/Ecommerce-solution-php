<?php
/**
 * @page Delete Mobile Message
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-mobile-message' );
$ajax->ok( $user, _('You must be signed in to delete a mobile message.') );

// Instantiate class
$m = new Mobile_Marketing();

// Delete user
$ajax->ok( $m->delete_message( $_GET['mmid'] ), _('An error occurred while trying to delete your mobile message. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();