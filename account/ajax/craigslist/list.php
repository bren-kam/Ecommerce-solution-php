<?php
/**
 * @page List Craigslist Templates
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$c = new Craigslist;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`title`', 'a.`text`', 'c.`name`', 'c.`sku`', '`status`', 'a.`date_created`' );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] );
$dt->search( array( 'a.`title`' => false, 'a.`text`' => true, 'c.`name`' => true, 'c.`sku`' => false ) );

// Get ads
$craigslist_ads = $c->get_craigslist_ads( $dt->get_variables() );
$dt->set_row_count( $c->count_craigslist_ads( $dt->get_where() ) );

$confirm_delete = _('Are you sure you want to delete a craigslist ad? This cannot be undone.');
$craigslist_ad_nonce = nonce::create( 'craigslist-ad' );

$data = array();

// Create output
if ( is_array( $craigslist_ads ) )
foreach ( $craigslist_ads as $ad ) {
	$status = ( $ad['date_posted'] + $ad['duration'] * 86400 > time() ) ? 
		intval( ( ( $ad['date_posted']  + intval( $ad['duration'] ) * 86400 ) - time() ) / 86400 + 1 ) : 
		- 1;
	
	// Set the links
	$links = ( $status == -1 ) ? '<a href="/craigslist/add-edit/?cid=' . $ad['craigslist_ad_id'] . '" title=\'' . _('Edit') . ' "' . $ad['craigslist_ad_id'] . '"\' class="edit-craiglist-ad">' . _('Edit / Publish') . '</a> | ' : '';
	
	// Status message
	$status_message = ( $status == -1 ) ? _('Ready to Publish') : $status . ' ' . _('Days Remaining');
	
	$data[] = array( $ad['title'] . '<br />
					<div class="actions">' . 
						$links .
						'<a href="/ajax/craigslist/copy/?cid=' . $ad['craigslist_ad_id'] . '&amp;_nonce=' . $craigslist_ad_nonce . '" title="' . _('Copy Craiglist Ad') . '" ajax="1">' . _('Copy') . '</a> | 
						<a href="/ajax/craigslist/delete/?cid=' . $ad['craigslist_ad_id'] . '&amp;_nonce=' . $craigslist_ad_nonce . '" title="' . _('Delete Craiglist Ad') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a>
					</div>',
					format::limit_chars( strip_tags( html_entity_decode( str_replace( "\n", '', $ad['text'] ) ) ), 45, NULL, TRUE ) . '...', 
					addslashes( $ad['product_name'] ),
					addslashes( $ad['sku'] ),
					addslashes( $status_message ),
					addslashes( dt::date( 'n/j/Y' , $ad['date_created'] ) ) );
}

// Send response
echo $dt->get_response( $data );