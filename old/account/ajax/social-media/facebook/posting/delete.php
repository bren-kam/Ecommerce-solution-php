<?php
/**
 * @page Delete Posting post
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-post' );
$ajax->ok( $user, _('You must be signed in to delete an post.') );

// Instantiate class
$s = new Social_Media();

// Delete user
$ajax->ok( $s->delete_posting_post( $_GET['sppid'] ), _('An error occurred while trying to delete your post. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();