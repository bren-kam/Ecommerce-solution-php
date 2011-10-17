<?php
/**
 * @page Create a Ticket
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_ajax_create_ticket'], 'create-ticket' );
$ajax->ok( $user, _('You must be signed in to create a ticket.') );

$t = new Tickets;

if ( '0' == $_POST['hTicketID'] ) {
	$ajax->ok( $t->create( $_POST['tTicketSummary'], $_POST['taTicket'] ), _('An error occurred while trying to create your ticket. Please refresh the page and try again') );
} else {
	$ajax->ok( $t->update( $_POST['hTicketID'], $_POST['tTicketSummary'], $_POST['taTicket'], $_POST['hTicketImages'] ), _('An error occurred while trying to create your ticket. Please refresh the page and try again') );
}

// Close the window
jQuery('a.close:first')->click();

// Don't want the attachments coming up next time
jQuery('#ticket-attachments')->empty();

// Reset the two fields
jQuery('#tTicketSummary, #taTicket')->val('')->blur();

// Add the jQuery
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send the response
$ajax->respond();