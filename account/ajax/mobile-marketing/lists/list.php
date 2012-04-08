<?php
/**
 * @page List Mobile Lists
 * @package Grey Suit Apps
 * @subpackage Account
 */

// Instantiate classes
$m = new Mobile_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`name`', 'a.`frequency`', 'a.`description`', 'a.`date_created`' );
$dt->add_where( " AND a.`website_id` = " . (int) $user['website']['website_id'] );
$dt->search( array( 'a.`name`' => false, 'a.`frequency`' => false, 'a.`description`' => true ) );

// Get lists
$mobile_lists = $m->list_mobile_lists( $dt->get_variables() );
$dt->set_row_count( $m->count_mobile_lists( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this mobile list? This cannot be undone.');
$delete_mobile_list_nonce = nonce::create( 'delete-mobile-list' );

// Create output
if ( is_array( $mobile_lists ) )
foreach ( $mobile_lists as $ml ) {
    $date = new DateTime( $ml['date_created'] );

	$data[] = array(
		$ml['name'] . ' (' . $ml['count'] . ')<br /><div class="actions"><a href="/mobile-marketing/subscribers/?mlid=' . $ml['mobile_list_id'] . '" title="' . _('View Subscribers') . '">' . _('View Subscribers') . '</a> |
						<a href="/mobile-marketing/lists/add-edit/?mlid=' . $ml['mobile_list_id'] . '" title="' . _('Edit') . '">' . _('Edit') . '</a> |
						<a href="/ajax/mobile-marketing/lists/delete/?mlid=' . $ml['mobile_list_id'] . '&amp;_nonce=' . $delete_mobile_list_nonce . '" title="' . _('Delete Mobile List') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>'
		, $ml['frequency']
        , format::limit_chars( $ml['description'], 75 )
		, $date->format( 'F jS, Y' )
	);
}

// Send response
echo $dt->get_response( $data );