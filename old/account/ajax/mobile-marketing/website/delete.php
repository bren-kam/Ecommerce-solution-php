<?php
/**
 * @page Delete Page
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-mobile-page' );
$ajax->ok( $user, _('You must be signed in to delete a page.') );

// Instantiate class
$m = new Mobile_Marketing;

// Delete user
$ajax->ok( $m->delete_mobile_page( $_GET['mpid'] ), _('An error occurred while trying to delete your page. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();