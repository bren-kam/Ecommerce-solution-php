<?php
/**
 * @page Create a Ticket
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_ajax_create_ticket'], 'create-ticket' );
$ajax->ok( $user, _('You must be signed in to create a ticket.') );

$t = new Tickets;

if ( '0' == $_POST['hTicketID'] ) {
	$ajax->ok( $t->create( strip_tags( $_POST['tTicketSummary'] ), format::links_to_anchors( $_POST['taTicket'], true, true ) ), _('An error occurred while trying to create your ticket. Please refresh the page and try again') );
} else {
	$ajax->ok( $t->update( $_POST['hTicketID'], strip_tags( $_POST['tTicketSummary'] ), format::links_to_anchors( $_POST['taTicket'], true, true ), $_POST['hTicketImages'] ), _('An error occurred while trying to create your ticket. Please refresh the page and try again') );
}

// Send the response
$ajax->respond();