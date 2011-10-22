<?php
/**
 * @page Support Ticketing - Upload File
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'ticket-upload' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// User is blank, must fill website
global $user;

// Instantiate class
$w = new Websites;
$f = new Files;

// We're in the admin section, we don't need a website id
$user = $u->get_user( $_POST['uid'] );
$user['website']['website_id'] = 0;

if ( '0' == $_POST['tid'] ) {
	// Instantiate the class
	$t = new Tickets;
	
	// Create an empty ticket
	$ticket_id = $t->create_empty();
} else {
	// Use the existing ticket
	$ticket_id = (int) $_POST['tid'];
}

// Upload the attachment
list( $ticket_upload_id, $attachment_name ) = $f->upload_ticket_attachment( $_FILES['Filedata']['name'], $_FILES['Filedata']['tmp_name'], $ticket_id );

$ajax->add_response( 'ticket_id', $ticket_id );
$ajax->add_response( 'ticket_upload_id', $ticket_upload_id );
$ajax->add_response( 'attachment_name', $attachment_name );
$ajax->add_response( 'delete_attachment_nonce', nonce::create('delete-attachment') );

// Send response
$ajax->respond();