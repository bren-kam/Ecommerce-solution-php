<?php
/**
 * @page Delete Attachment
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-attachment' );
$ajax->ok( $user, _('You must be signed in to delete an attachment.') );

// Type Juggling
$ticket_upload_id = (int) $_GET['tuid'];

// Instantiate Class
$f = new Files;

// Delete uploaded
$ajax->ok( $f->remove_upload( $ticket_upload_id ), _('An error occurred while trying to remove your attachment. Please refresh the page and try again.') );

// Redraw the table
jQuery("#pAttachment{$ticket_upload_id}")->remove();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();