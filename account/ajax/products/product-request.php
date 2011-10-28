<?php
/**
 * @page Product Request
 * @package Imagine Retailer
 */
error_reporting(E_ALL);
// Create new AJAX
$ajax = new AJAX( $_POST['_ajax_product_request'], 'product-request' );
$ajax->ok( $user, _('You must be signed in to send a product request.') );

// Add the request
$t = new Tickets;

$ticket_message = '';

foreach ( $_POST['requests'] as $r ) {
	if ( !empty( $ticket_message ) )
		$ticket_message .= "\n\n";
	
	// Get the brand, sku and collection
	$ticket_array = explode( '|', $r );
	
	// Add it to the message
	$ticket_message .= 'Brand: ' . $ticket_array[0] . "\n";
	$ticket_message .= 'SKU: ' . $ticket_array[1] . "\n";
	$ticket_message .= 'Collection: ' . $ticket_array[2];
	
	$subject = ( $user['website']['live'] ) ? 'Live' : 'Staging';
}

$ajax->ok( $t->create( "$subject - Product Request", $ticket_message ), _('An error occurred while trying to create your request. Please refresh the page and try again.') );

// Empty the list
jQuery('#dRequestList')->empty();

// Close Dialog
jQuery('#aClose')->click();

// Add the jQuery Response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();