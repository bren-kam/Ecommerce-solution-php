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
	// Create and get ticket id
    $ajax->ok( $ticket_id = $t->create( format::links_to_anchors( $_POST['tTicketSummary'] ), $_POST['taTicket'] ), _('An error occurred while trying to create your ticket. Please refresh the page and try again') );
} else {
    // Update ticket
	$ajax->ok( $t->update( $_POST['hTicketID'], format::links_to_anchors( $_POST['tTicketSummary'] ), $_POST['taTicket'], $_POST['hTicketImages'] ), _('An error occurred while trying to create your ticket. Please refresh the page and try again') );

    // Get ticket id
    $ticket_id = $_POST['hTicketID'];
}

// Complete any checklist items that were specified
if ( $user['role'] >= 7 && is_array( $_POST['sChecklistItems'] ) ) {
    // Declare object
    $c = new Checklists;

    // Complete the items
    $c->complete_items( $ticket_id, $_POST['sChecklistItems'] );
}

// Close the window
jQuery('a.close:visible:first')->click();

// Don't want the attachments coming up next time
jQuery('#ticket-attachments')->empty();

// Reset the two fields
jQuery('#tTicketSummary, #taTicket')->val('')->blur();

// Remove any selected items
jQuery('#sChecklistItems option:checked')->remove();

// Remove any empty sections
jQuery('#sChecklistItems optgroup:not(:has(option))')->remove();

// Add the jQuery
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send the response
$ajax->respond();