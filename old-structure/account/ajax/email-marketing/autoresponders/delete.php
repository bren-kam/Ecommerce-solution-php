<?php
/**
 * @page Delete Autoresponder
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-autoresponder' );
$ajax->ok( $user, _('You must be signed in to delete an autoresponder.') );

// Instantiate class
$e = new Email_Marketing();

// Delete user
$ajax->ok( $e->delete_autoresponder( $_GET['eaid'] ), _('An error occurred while trying to delete your autoresponder. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();