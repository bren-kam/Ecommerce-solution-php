<?php
/**
 * @page List Autoresponders
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$m = new Mobile_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( '`name`', '`subject`' );
$dt->add_where( " AND `website_id` = " . (int) $user['website']['website_id'] );
$dt->search( array( '`name`' => false ) );

// Get autoresponder
$autoresponders = $m->list_autoresponders( $dt->get_variables() );
$dt->set_row_count( $m->count_autoresponders( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this autoresponder? This cannot be undone.');
$delete_autoresponder_nonce = nonce::create( 'delete-autoresponder' );

// Create output
if ( is_array( $autoresponders ) )
foreach ( $autoresponders as $a ) {
	$actions = ( $a['default'] ) ? '' : ' | <a href="/ajax/mobile-marketing/autoresponders/delete/?maid=' . $a['mobile_autoresponder_id'] . '&amp;_nonce=' . $delete_autoresponder_nonce . '" title="' . _('Delete Autoresponder') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>';

    $date = new DateTime( $a['date_created'] );

 	$data[] = array( 
		$a['name'] . '<br /><div class="actions"><a href="/mobile-marketing/autoresponders/add-edit/?maid=' . $a['mobile_autoresponder_id'] . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions . '</div>',
		$date->format('F jS, Y')
	);
}

// Send response
echo $dt->get_response( $data );