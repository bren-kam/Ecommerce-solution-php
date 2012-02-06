<?php
/**
 * @page List Subscribers
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$e = new Email_Marketing;
$dt = new Data_Table();

// Whether we are getting subscribed or unsubscribed
$status = (int) $_GET['s'];

// Set variables
$dt->order_by( 'a.`email`', 'a.`name`', 'a.`phone`', 'a.`date_created`' );
$dt->add_where( " AND a.`status` = $status" );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] );
$dt->search( array( 'a.`email`' => false, 'a.`name`' => false ) );

// Find out whether it's for a specific email list id or not
if ( isset( $_GET['elid'] ) ) {
	$dt->add_where( " AND b.`email_list_id` = " . (int) $_GET['elid'] );

	// Get subscribers by email list id
	$subscribers = $e->list_subscribers_by_email_list_id( $dt->get_variables() );
	$dt->set_row_count( $e->count_subscribers_by_email_list_id( $dt->get_where() ) );
} else {
	// Get subscribers
	$subscribers = $e->list_subscribers( $dt->get_variables() );
	$dt->set_row_count( $e->count_subscribers( $dt->get_where() ) );
}

// Only if they are subscribed
if ( $status ) {
	$confirm = _('Are you sure you want to unsubscribe this email? This cannot be undone.');
	$unsubscribe_email_nonce = nonce::create( 'unsubscribe-email' );
}
	
// Initialize variable
$data = array();
	
// Create output
if ( is_array( $subscribers ) )
foreach ( $subscribers as $s ) {
	$actions = ( $status ) ? ' | <a href="/ajax/email-marketing/subscribers/unsubscribe/?eid=' . $s['email_id'] . '&amp;e=' . $s['email'] . '&amp;_nonce=' . $unsubscribe_email_nonce . '"  title="' . _('Unsubscribe Email') . '" ajax="1" confirm="' . $confirm . '">' . _('Unsubscribe') . '</a>' : '';

	$data[] = array( 
		$s['email'] . '<br /><div class="actions"><a href="/email-marketing/subscribers/add-edit/?eid=' . $s['email_id'] . '" title="' . _('Edit Subscriber') . '">' . _('Edit Subscriber') . '</a>' . $actions . '</div>',
		$s['name'],
		$s['phone'],
		dt::date( 'F jS, Y g:i a', $s['date'] )
	);
}

// Send response
echo $dt->get_response( $data );