<?php
/**
 * @page List Shipping Methods (Ajax)
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$sc = new Shopping_Cart;
$dt = new Data_Table;

// Set variables
$dt->order_by( '`name`', '`method`', '`amount`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`name`' => false ) );

// Get methods
$shipping_methods = $sc->list_shipping_methods( $dt->get_variables() );
$dt->set_row_count( $sc->count_shipping_methods( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this shipping method? This cannot be undone.');
$delete_shipping_method_nonce = nonce::create( 'delete-shipping-method' );

if ( is_array( $shipping_methods ) )
foreach ( $shipping_methods as $sm ) {
	$percentage = ( 'Percentage' == $sm['method'] ) ? true : false;
	
	switch ( $sm['type'] ) {
		case 'custom':
			$type = 'Custom';
			$name = $sm['name'];
		break;
		
		case 'fedex':
			$type = 'FedEx';
			$name = ucwords( strtolower( str_replace( '_', ' ', $sm['name'] ) ) );
		break;
		
		case 'ups':
			$type = 'UPS';
			
			$services = array(
				'02' => _('UPS Second Day Air')
				, '03' => _('UPS Ground')
				, '07' => _('UPS Worldwide Express')
				, '08' => _('UPS Worldwide Expedited')
				, '11' => _('UPS Standard')
				, '12' => _('UPS Three-Day Select')
				, '13' => _('Next Day Air Saver')
				, '14' => _('UPS Next Day Air Early AM')
				, '54' => _('UPS Worldwide Express Plus')
				, '59' => _('UPS Second Day Air AM')
				, '65' => _('UPS Saver')
			);
			
			$name = $services[$sm['name']];
		break;
		
		case 'USPS':
			$type = 'USPS';
			$name = ucwords( strtolower( $sm['name'] ) );
		break;
	}
	
	$data[] = array(
		$name . '<br /><div class="actions"><a href="/shopping-cart/shipping/add-edit-' . $sm['type'] . '/?wsmid=' . $sm['website_shipping_method_id'] . '" title="' . _('Edit Zip Codes') . '">' . _('Edit') . '</a> | 
				<a href="/ajax/shopping-cart/shipping/delete/?wsmid=' . $sm['website_shipping_method_id'] . '&amp;_nonce=' . $delete_shipping_method_nonce . '" title="' . _('Delete Shipping Method') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
			</small>'
		, $type
		, $sm['method']
		, ( ( !$percentage ) ? '$' : '' ) . number_format( $sm['amount'], 2 ) . ( ( $percentage ) ? '%' : '' )
	);
}

// Send response
echo $dt->get_response( $data );