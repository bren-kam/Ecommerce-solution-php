<?php
/**
 * @page Support Ticketing - Upload File
 * @package Grey Suit Retail
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

// Fill the global $user['website']
$user = $u->get_user( $_POST['uid'] );
$user['website'] = $w->get_website( $_POST['wid'] );

if ( '0' == $_POST['tid'] ) {
	// Instantiate the class
	$t = new Tickets;
	
	// Create an empty ticket
	$ticket_id = $t->create_empty();
	
	// Change the ticket ID
	jQuery('#hTicketID')->val( $ticket_id );
} else {
	// Use the existing ticket
	$ticket_id = (int) $_POST['tid'];
}

// Upload the attachment
list( $ticket_upload_id, $attachment_name ) = $f->upload_attachment( $_FILES['Filedata']['name'], $_FILES['Filedata']['tmp_name'], $ticket_id );

// Add the new link and apply sparrow to it
jQuery('#ticket-attachments')
	->show()
	->append( '<p id="pAttachment' . $ticket_upload_id . '">' . $attachment_name . '<input type="hidden" name="hTicketImages[]" value="' . $ticket_upload_id . '" /> <a href="/ajax/support/delete-attachment/?_nonce=' . nonce::create('delete-attachment') . '&amp;tuid=' . $ticket_upload_id . '" title="' . _('Remove Attachment') . '" ajax="1" confirm="' . _('Are you sure you want to remove this attachment? This cannot be undone.') . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Remove Attachment'). '" /></a></p>' )
	->sparrow();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();