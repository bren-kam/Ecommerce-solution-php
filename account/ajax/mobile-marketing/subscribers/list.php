<?php
/**
 * @page List Subscribers
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$e = new Mobile_Marketing;
$dt = new Data_Table();

// Whether we are getting subscribed or unsubscribed
$status = (int) $_GET['s'];

// Set variables
$dt->order_by( 'a.`phone`', 'a.`date_created`' );
$dt->add_where( " AND a.`status` = $status" );
$dt->add_where( " AND a.`website_id` = " . (int) $user['website']['website_id'] );
$dt->search( array( 'a.`phone`' => false ) );

// Find out whether it's for a specific mobile list id or not
if ( isset( $_GET['mlid'] ) ) {
	$dt->add_where( " AND b.`mobile_list_id` = " . (int) $_GET['mlid'] );

	// Get subscribers by mobile list id
	$subscribers = $e->list_subscribers_by_mobile_list_id( $dt->get_variables() );
	$dt->set_row_count( $e->count_subscribers_by_mobile_list_id( $dt->get_where() ) );
} else {
	// Get subscribers
	$subscribers = $e->list_subscribers( $dt->get_variables() );
	$dt->set_row_count( $e->count_subscribers( $dt->get_where() ) );
}

// Only if they are subscribed
if ( $status ) {
	$confirm = _('Are you sure you want to unsubscribe this subscriber? This cannot be undone.');
	$unsubscribe_subscriber_nonce = nonce::create( 'unsubscribe-subscriber' );
}
	
// Initialize variable
$data = array();
	
// Create output
if ( is_array( $subscribers ) )
foreach ( $subscribers as $s ) {
	$actions = ( $status ) ? ' | <a href="/ajax/mobile-marketing/subscribers/unsubscribe/?msid=' . $s['mobile_subscriber_id'] . '&amp;_nonce=' . $unsubscribe_subscriber_nonce . '"  title="' . _('Unsubscribe') . '" ajax="1" confirm="' . $confirm . '">' . _('Unsubscribe') . '</a>' : '';
    $date = new DateTime( $s['date'] );

	$data[] = array( 
		$s['phone'] . '<br /><div class="actions"><a href="/mobile-marketing/subscribers/add-edit/?msid=' . $s['mobile_subscriber_id'] . '" title="' . _('Edit Subscriber') . '">' . _('Edit Subscriber') . '</a>' . $actions . '</div>',
		$date->format( 'F jS, Y g:i a')
	);
}

// Send response
echo $dt->get_response( $data );