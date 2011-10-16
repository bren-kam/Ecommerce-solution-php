<?php
/**
 * @page Delete Authorized user
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-authorized-user' );
$ajax->ok( $user, _('You must be signed in to delete an authorized user.') );
$ajax->ok( 1 != $user['role'], _('You do not have permission to delete this authorized user.') );

// Instantiate class
$au = new Authorized_Users();

// Delete user
$ajax->ok( $au->delete( $_GET['uid'], $user['website']['website_id'] ), _('An error occurred while trying to delete your authorized user. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();