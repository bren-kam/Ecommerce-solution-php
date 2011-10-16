<?php
/**
 * @page Delete Email List
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-email-list' );
$ajax->ok( $user, _('You must be signed in to delete an email list.') );

// Instantiate class
$e = new Email_Marketing();

// Delete user
$ajax->ok( $e->delete_email_list( $_GET['elid'] ), _('An error occurred while trying to delete your email list. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();