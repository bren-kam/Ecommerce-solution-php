<?php
/**
 * @page List Email Lists
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$e = new Email_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`name`', 'a.`description`', 'date_created' );
$dt->add_where( " AND c.`website_id` = " . $user['website']['website_id'] );
$dt->search( array( 'a.`name`' => false, 'a.`description`' => true ) );

// Get messages
$email_lists = $e->list_email_lists( $dt->get_variables() );

$dt->set_row_count( $e->count_email_lists( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this email list? This cannot be undone.');
$delete_email_list_nonce = nonce::create( 'delete-email-list' );

// Create output
if( is_array( $email_lists ) )
foreach( $email_lists as $el ) {
	$data[] = array( 
		$el['name'] . ' (' . $el['count'] . ')<br /><div class="actions"><a href="/email-marketing/subscribers/?elid=' . $el['email_list_id'] . '" title="' . _('View Subscribers') . '">' . _('View Subscribers') . '</a> | 
						<a href="/email-marketing/email-lists/add-edit/?elid=' . $el['email_list_id'] . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | 
						<a href="/ajax/email-marketing/email-lists/delete/?elid=' . $el['email_list_id'] . '&amp;_nonce=' . $delete_email_list_nonce . '" title="' . _('Delete Email List') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>',
		format::limit_chars( $el['description'], 32, '...' ),
		date_time::date( 'F jS, Y g:i a', $el['date_created'] )
	);
}

// Send response
echo $dt->get_response( $data );