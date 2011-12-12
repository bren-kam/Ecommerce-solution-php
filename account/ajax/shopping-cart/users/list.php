<?php
/**
 * @page List Shopping Cart Users
 * @package Imagine Retailer
 */

// Instantiate classes
$sc = new Shopping_Cart;
$dt = new Data_Table;

// Set variables
$dt->order_by( '`email`', '`billing_first_name`', '`status`', '`date_registered`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`email`' => false, '`billing_first_name`' => false ) );

// Get methods
$users = $sc->list_users( $dt->get_variables() );
$dt->set_row_count( $sc->count_users( $dt->get_where() ) );

$data = array();
$delete_user_nonce = nonce::create( 'delete-user' );
$confirm_delete = _('Are you sure you want to delete this user? This cannot be undone.');

// Format Data
if ( is_array( $users ) )
foreach ( $users as $usr ) {
	$data[] = array(
		$usr['email'] . '<br />
		<div class="actions">' . 
			'<a href="/shopping-cart/users/edit/?uid=' . $usr['website_user_id'] . '" title="' . _('Edit User') . '">' . _('Edit') . '</a> | 
			<a href="/ajax/shopping-cart/users/delete/?uid=' . $usr['website_user_id'] . '&amp;_nonce=' . $delete_user_nonce . '" title="' . _('Delete User') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a>
		</div>',
		 $usr['billing_first_name'],
		$usr['status'],
		dt::date( 'F j, Y', $usr['date_registered'] )
	);
}

// Send response
echo $dt->get_response( $data );