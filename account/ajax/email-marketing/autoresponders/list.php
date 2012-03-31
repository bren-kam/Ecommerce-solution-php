<?php
/**
 * @page List Autoresponders
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$e = new Email_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( '`name`', '`subject`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`name`' => false, '`subject`' => true ) );

// Get autoresponder
$autoresponders = $e->list_autoresponders( $dt->get_variables() );
$dt->set_row_count( $e->count_autoresponders( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this autoresponder? This cannot be undone.');
$delete_autoresponder_nonce = nonce::create( 'delete-autoresponder' );

// Create output
if ( is_array( $autoresponders ) )
foreach ( $autoresponders as $a ) {
	$actions = ( $a['default'] ) ? '' : ' | <a href="/ajax/email-marketing/autoresponders/delete/?eaid=' . $a['email_autoresponder_id'] . '&amp;_nonce=' . $delete_autoresponder_nonce . '" title="' . _('Delete Autoresponder') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>';
	
 	$data[] = array( 
		$a['name'] . '<br /><div class="actions"><a href="/email-marketing/autoresponders/add-edit/?eaid=' . $a['email_autoresponder_id'] . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions . '</div>',
		format::limit_chars( $a['subject'], 100 )
	);
}

// Send response
echo $dt->get_response( $data );