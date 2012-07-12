<?php
/**
 * @page List Shipping Methods (Ajax)
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$w = new Websites;
$sc = new Shopping_Cart;
$dt = new Data_Table();

$shipping_methods = $sc->get_shipping_methods( $user['website']['website_id'] );

if( is_array( $shipping_methods ) && count( $shipping_methods ) > 0 ) {
	foreach( $shipping_methods as $sm ) {
		$percentage = ( 'Percentage' == $sm['method'] ) ? true : false;
		$data[] = array(
		
			'<span>' . $sm['name'] . '</span><br/>
				<small>
					<a href="/shopping-cart/add-edit-shipping/?wsmid=' . $sm['website_shipping_method_id'] . '" title="' . _('Edit Zip Codes') . '">' . _('Edit') . '</a> | 
					<a ajax="1" href="/ajax/shopping-cart/delete-shipping-method/?wsmid=' . $sm['website_shipping_method_id'] . '&_nonce=' . nonce::create( 'delete-shipping-method' ) . '" id="aDelete' . $sm['website_shipping_method_id'] . '" class="delete-shipping-method" title="Delete Shipping Method">' . _('Delete') . '</a>
				</small>',
			$sm['method'],
			( ( !$percentage ) ? '$' : '' ) . number_format( $sm['amount'], 2 ) . ( ( $percentage ) ? '%' : '' )
		);
	}
}

// Send response
echo $dt->get_response( $data );