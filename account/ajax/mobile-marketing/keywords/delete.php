<?php
/**
 * @page Delete Keyword
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-keyword' );
$ajax->ok( $user, _('You must be signed in to delete a keyword.') );

// Instantiate class
$m = new Mobile_Marketing();
$ajax->ok( $m->delete_keyword( $_GET['mkid'] ), _('An error occurred while trying to delete this keyword.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();