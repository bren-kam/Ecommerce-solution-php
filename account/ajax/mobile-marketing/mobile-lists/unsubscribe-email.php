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
// 1) $email_lists = FUNCTION TO RETRIEVE A LIST OF ALL INSTANCES OF THIS EMAIL_ID, THE LIST THEY'RE ON, AND THE WEBSITE_ID OF THAT LIST
// 2) Remove the list related to this website_id from $email_lists
// 3) $ajax->ok( $e->update_email_list_subscriptions( $_GET['eid'], $email_lists ), _('An error occurred while trying to unsubscribe this email.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();