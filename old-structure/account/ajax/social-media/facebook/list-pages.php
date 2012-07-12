<?php
/**
 * @page List Facebook Pages
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$s = new Social_Media();
$dt = new Data_Table();
$w = new Websites;

// Set variables
$dt->order_by( '`name`', '`date_created`' );
$dt->search( array( '`name`' => false ) );

// Get autoresponder
$facebook_pages = $s->list_facebook_pages( $dt->get_variables() );
$dt->set_row_count( $s->count_facebook_pages( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this post? This will disable all related apps and it cannot be undone.');
$delete_facebook_page_nonce = nonce::create( 'delete-facebook-page' );
$timezone = $w->get_setting( 'timezone' );

// Create output
if ( is_array( $facebook_pages ) )
foreach ( $facebook_pages as $fb_page ) {
	// Set the actions
    $actions = '<br />
	<div class="actions">
		<a href="/social-media/facebook/choose/?smfbpid=' . $fb_page['id'] . '" title="' . _('Select Page') . '">' . _('Select') . '</a> |
		<a href="/social-media/facebook/add-edit/?smfbpid=' . $fb_page['id'] . '" title="' . _('Edit Page') . '">' . _('Edit') . '</a> |
		<a href="/ajax/social-media/facebook/delete-page/?smfbpid=' . $fb_page['id'] . '&amp;_nonce=' . $delete_facebook_page_nonce . '" title="' . _('Delete Page') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
	</div>';
	
 	$data[] = array(
		$fb_page['name'] . $actions,
        dt::adjust_timezone( $fb_page['date_created'], config::setting('server-timezone'), $timezone, 'F jS, Y g:i a' )
	);
}

// Send response
echo $dt->get_response( $data );