<?php
/**
 * @page List Mobile Lists
 * @package Grey Suit Apps
 * @subpackage Account
 */

// Instantiate classes
$e = new Mobile_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`name`', 'date_created' );
$dt->add_where( " AND a.`website_id` = " . (int) $user['website']['website_id'] );
$dt->search( array( 'a.`name`' => false ) );

// Get lists
$mobile_lists = $e->list_mobile_lists( $dt->get_variables() );

$dt->set_row_count( $e->count_mobile_lists( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this mobile list? This cannot be undone.');
$delete_mobile_list_nonce = nonce::create( 'delete-mobile-list' );

// Create output
if ( is_array( $mobile_lists ) )
foreach ( $mobile_lists as $ml ) {
    $date = new DateTime( $ml['date_created'] );

	$data[] = array(
		$ml['name'] . ' (' . $ml['count'] . ')<br /><div class="actions"><a href="/mobile-marketing/subscribers/?mlid=' . $ml['mobile_list_id'] . '" title="' . _('View Subscribers') . '">' . _('View Subscribers') . '</a> |
						<a href="/mobile-marketing/mobile-lists/add-edit/?elid=' . $ml['mobile_list_id'] . '" title="' . _('Edit') . '">' . _('Edit') . '</a> |
						<a href="/ajax/mobile-marketing/mobile-lists/delete/?elid=' . $ml['mobile_list_id'] . '&amp;_nonce=' . $delete_mobile_list_nonce . '" title="' . _('Delete Mobile List') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>',
		$date->format( 'F jS, Y' )
	);
}

// Send response
echo $dt->get_response( $data );