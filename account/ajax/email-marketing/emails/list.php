<?php
/**
 * @page List Emails
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$e = new Email_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( '`subject`', '`status`', 'date_sent' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`subject`' => true ) );

// Get messages
$messages = $e->list_email_messages( $dt->get_variables() );
$dt->set_row_count( $e->count_email_messages( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this email? This cannot be undone.');
$delete_email_message_nonce = nonce::create( 'delete-email-message' );
$statuses = array( 'Draft', 'Scheduled', 'Sent' );

// Initialize variable
$data = array();

// Create output
if ( is_array( $messages ) )
foreach ( $messages as $m ) {
	if ( $m['status'] < 2 ) {
		$actions = '<a href="/email-marketing/emails/send/?emid=' . $m['email_message_id'] . '" title="' . _('Edit Message') . '">' . _('Edit') . '</a> | ';
		$actions .= '<a href="/ajax/email-marketing/emails/delete/?emid=' . $m['email_message_id'] . '&amp;_nonce=' . $delete_email_message_nonce . '" title="' . _('Delete Email Message') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
	} else {
		$actions = '<a href="/analytics/email-marketing/email/?mcid=' . $m['mc_campaign_id'] . '" title="' . _('View Analytics') . '">' . _('Analytics') . '</a>';
	}
	
	$data[] = array( 
		format::limit_chars( $m['subject'], 50, '...' ) . '<br /><div class="actions">' . $actions . '</div>',
		$statuses[$m['status']],
		dt::date( 'F jS, Y g:i a', $m['date_sent'] )
	);
}

// Send response
echo $dt->get_response( $data );