<?php
/**
 * @page Delete Email List
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'unsubscribe-email' );
$ajax->ok( $user, _('You must be signed in to unsubscribe a user.') );

// Instantiate class
$e = new Email_Marketing();
$ajax->ok( $e->unsubscribe( $_GET['eid'], $_GET['e'] ), _('An error occurred while trying to unsubscribe this email.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();