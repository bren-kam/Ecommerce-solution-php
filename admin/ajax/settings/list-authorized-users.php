<?php
/**
 * @page List Authorized Users
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$au = new Authorized_Users;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`email`', 'b.`pages`', 'b.`products`', 'b.`analytics`', '`b.blog`', 'b.`email_marketing`', 'b.`shopping_cart`' );
$dt->add_where( " AND b.`website_id` = " . $user['website']['website_id'] );
$dt->search( array( 'a.`email`' => false ) );

// Get authorized users
$authorized_users = $au->list_authorized_users( $dt->get_variables() );
$dt->set_row_count( $au->count_authorized_users( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this Authorized user? This cannot be undone.');
$delete_authorized_user_nonce = nonce::create( 'delete-authorized-user' );

// Create output
if ( is_array( $authorized_users ) )
foreach ( $authorized_users as $u ) {
	$data[] = array( $u['email'] . '<br />
					<div class="actions">
						<a href="/settings/add-edit-authorized-user/?uid=' . $u['user_id'] . '" title="' . _('Edit Authorized User') . '">' . _('Edit') . '</a> |
						<a href="/ajax/settings/delete-authorized-user/?uid=' . $u['user_id'] . '&amp;_nonce=' . $delete_authorized_user_nonce . '" title="' . _('Delete Authorized User') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
					</div>',
					human( $u['pages'] ),
					human( $u['products'] ),
					human( $u['analytics'] ),
					human( $u['blog'] ),
					human( $u['email_marketing'] ),
					human( $u['shopping_cart'] )
					);
}


// Return human bool
function human( $bool ) {
	return ( '1' == $bool ) ? 'Yes' : 'No';
}

// Send response
echo $dt->get_response( $data );