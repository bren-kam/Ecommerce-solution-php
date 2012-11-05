<?php
/**
 * @page Delete Email Message
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-email-message' );
$ajax->ok( $user, _('You must be signed in to delete an email message.') );

// Instantiate class
$e = new Email_Marketing();

// Delete user
$ajax->ok( $e->delete_email_message( $_GET['emid'] ), _('An error occurred while trying to delete your email message. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();