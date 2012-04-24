<?php
/**
 * @page List Craigslist Templates
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$c = new Craigslist;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'b.`headline`', 'a.`text`', 'c.`name`', 'c.`sku`', 'a.`active`', 'a.`date_created`' );
$dt->add_where( " AND a.`website_id` = " . (int) $user['website']['website_id'] );
$dt->search( array( 'b.`headline`' => false, 'a.`text`' => true, 'c.`name`' => true, 'c.`sku`' => false ) );

// Get ads
$craigslist_ads = $c->get_craigslist_ads( $dt->get_variables() );
$dt->set_row_count( $c->count_craigslist_ads( $dt->get_where() ) );

$confirm_delete = _('Are you sure you want to delete a craigslist ad? This cannot be undone.');
$delete_craigslist_ad_nonce = nonce::create( 'delete-craigslist-ad' );
$copy_craigslist_ad_nonce = nonce::create( 'copy-craigslist-ad' );

$data = array();

// Create output
if ( is_array( $craigslist_ads ) )
foreach ( $craigslist_ads as $ad ) {
    $status = ( '0000-00-00 00:00:00' == $ad['date_posted'] ) ? _('Waiting Approval') : _('Posted');

    // Get the date
    $date = new DateTime( $ad['date_created'] );

	$data[] = array(
        $ad['headline'] . '<br />
        <div class="actions">' .
            '<a href="/craigslist/add-edit/?caid=' . $ad['craigslist_ad_id'] . '" title="' . _('Edit') . '">' . _('Edit / Post') . '</a> | ' .
            '<a href="/ajax/craigslist/copy/?caid=' . $ad['craigslist_ad_id'] . '&amp;_nonce=' . $copy_craigslist_ad_nonce . '" title="' . _('Copy Craiglist Ad') . '" ajax="1">' . _('Copy') . '</a> | ' .
            '<a href="/ajax/craigslist/delete/?caid=' . $ad['craigslist_ad_id'] . '&amp;_nonce=' . $delete_craigslist_ad_nonce . '" title="' . _('Delete Craiglist Ad') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a>
        </div>'
        , format::limit_chars( html_entity_decode( str_replace( "\n", '', $ad['text'] ) ), 100, NULL, TRUE ) . '...'
        , $ad['product_name']
        , $ad['sku']
        , $status
        , $date->format('n/j/Y')
    );
}

// Send response
echo $dt->get_response( $data );