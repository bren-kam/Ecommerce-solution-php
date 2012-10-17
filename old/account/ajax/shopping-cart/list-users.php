<?php
/**
 * @page List Craigslist Templates
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$c = new Shopping_Cart;
$dt = new Data_Table();

$results = $c->get_users( $user['website']['website_id'] );
$data = array();

$shopping_cart_nonce = nonce::create( 'shopping-cart' );

$confirm_delete = _('Are you sure you want to delete this user? This cannot be undone.');

// Format Data
foreach( $results as $result ) {
	$data[] = array(
		$result['email'] . '<br />
		<div class="actions">' . 
			$links .
			'<a href="/shopping-cart/edit-user/?uid=' . $result['website_user_id'] . '" title="' . _('Edit User') . '">' . _('Edit') . '</a> | 
			<a href="/ajax/shopping-cart/delete-user/?uid=' . $result['website_user_id'] . '&amp;_nonce=' . $shopping_cart_nonce . '" title="' . _('Delete User') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a>
		</div>',
		addslashes( $result['billing_first_name'] ),
		addslashes( $result['status'] ),
		addslashes( date( 'F j, Y', $result['date_registered'] ) )
	);
}

// Send response
echo $dt->get_response( $data );