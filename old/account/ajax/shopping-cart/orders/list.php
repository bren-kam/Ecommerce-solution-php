<?php
/**
 * @page List Orders
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$c = new Shopping_Cart;
$dt = new Data_Table();

$dt->order_by( '`website_order_id`', '`total_cost`', '`status`', '`date_created`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );

$results = $c->get_orders( $dt->get_variables() );
$dt->set_row_count( $c->count_orders( $user['website']['website_id'] ) );

$data = array();

// Format Data
foreach( $results as $result ) {
    switch ( $result['status'] ) {
        case -1:
            $status = _('Declined');
        break;

        case 0:
            $status = _('Purchased');
        break;

        case 1:
            $status = _('Pending');
        break;

        case 2:
            $status = _('Delivered');
        break;
    }

	$data[] = array(
		'<a href="/shopping-cart/orders/view/?oid=' . $result['website_order_id'] . '" title="' . _('View Order') . '">' . $result['website_order_id'] . '</a>'
		, '$' . number_format( $result['total_cost'], 2 )
		, $status
		, dt::date( 'F j, Y', $result['date_created'] )
	);
}

// Send response
echo $dt->get_response( $data );