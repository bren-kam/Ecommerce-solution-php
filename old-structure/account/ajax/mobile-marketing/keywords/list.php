<?php
/**
 * @page List Keywords
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$e = new Mobile_Marketing;
$dt = new Data_Table();

// Set variables
$dt->order_by( '`keyword`', '`response`', '`date_created`' );
$dt->add_where( " AND `website_id` = " . (int) $user['website']['website_id'] );
$dt->search( array( '`keyword`' => false, '`response`' => true ) );

// Get Keywords
$keywords = $e->list_keywords( $dt->get_variables() );
$dt->set_row_count( $e->count_keywords( $dt->get_where() ) );

// Only if they are subscribed
$confirm = _('Are you sure you want to delete this keyword? This cannot be undone and we cannot guarantee that you get this keyword again.');
$delete_nonce = nonce::create( 'delete-keyword' );

// Initialize variable
$data = array();
	
// Create output
if ( is_array( $keywords ) )
foreach ( $keywords as $k ) {
    $date = new DateTime( $k['date_created'] );

	$data[] = array( 
		$k['keyword'] . '<div class="actions"><a href="/mobile-marketing/keywords/add-edit/?mkid=' . $k['mobile_keyword_id'] . '" title="' . _('Edit Keyword') . '">' . _('Edit Keyword') . '</a> | <a href="/ajax/mobile-marketing/keywords/delete/?mkid=' . $k['mobile_keyword_id'] . '&amp;_nonce=' . $delete_nonce . '"  title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>'
        , format::limit_chars( $k['response'] )
		, $date->format( 'F jS, Y g:i a')
	);
}

// Send response
echo $dt->get_response( $data );