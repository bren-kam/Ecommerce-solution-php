<?php
/**
 * @page List Product Coupons
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$c = new Coupons;
$dt = new Data_Table;

// Set variables
$dt->order_by( '`name`', '`amount`', '`type`', '`item_limit`', '`date_created`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`name`' => true ) );

// Get coupons
$coupons = $c->list_coupons( $dt->get_variables() );
$dt->set_row_count( $c->count_coupons( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this coupon? This cannot be undone.');
$delete_coupon_nonce = nonce::create( 'delete-coupon' );
								
// Create output
if( is_array( $coupons ) )
foreach( $coupons as $coupon ) {
	$actions = '<a href="/products/coupons/add-edit/?wcid=' . $coupon['website_coupon_id'] . '" title="' . _('Edit Coupon') . '">' . _('Edit') . '</a>';
	$actions .= ' | <a href="/ajax/products/coupons/delete/?wcid=' . $coupon['website_coupon_id'] . '&amp;_nonce=' . $delete_coupon_nonce . '" title="' . _('Delete Coupon') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
	
	$data[] = array( 
		$coupon['name'] . '<div class="actions">' . $actions . '</div>',
		number_format( $coupon['amount'], 2 ),
		$coupon['type'],
		$coupon['item_limit'],
		date_time::date( 'F jS, Y', $coupon['date_created'] )
	);
}
		
// Send response
echo $dt->get_response( $data );