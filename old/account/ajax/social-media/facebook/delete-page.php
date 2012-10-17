<?php
/**
 * @page Delete Posting post
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-facebook-page' );
$ajax->ok( $user, _('You must be signed in to delete a facebook page.') );

// Instantiate class
$s = new Social_Media();

// Delete user
$ajax->ok( $s->delete_facebook_page( $_GET['smfbpid'] ), _('An error occurred while trying to delete your facebook page. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();