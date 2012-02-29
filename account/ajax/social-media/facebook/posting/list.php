<?php
/**
 * @page List Posting posts
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$s = new Social_Media();
$dt = new Data_Table();
$w = new Websites;

// Set variables
$dt->order_by( '`post`', '`status`', '`date_posted`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`post`' => true ) );

// Get autoresponder
$posts = $s->list_posting_posts( $dt->get_variables() );
$dt->set_row_count( $s->count_posting_posts( $dt->get_where() ) );

$confirm = _('Are you sure you want to cancel this post? This cannot be undone.');
$delete_post_nonce = nonce::create( 'delete-post' );
$timezone = $w->get_setting( 'timezone' );

// Create output
if ( is_array( $posts ) )
foreach ( $posts as $p ) {
	// Set the actions
    $actions = '<br />
	<div class="actions">
		<a href="/ajax/social-media/facebook/posting/delete/?sppid=' . $p['sm_posting_post_id'] . '&amp;_nonce=' . $delete_post_nonce . '" title="' . _('Delete Post') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
	</div>';
	
	// Determine what to do based off the status
	switch ( $p['status'] ) {
		case -1:
			$status = _('Error');
			
			$p['post'] .= '<br /><br /><span class="error">' . $p['error'] . '</span>';
		break;
		
		case 0:
			$status = _('Scheduled');
		break;
		
		case 1:
			$actions = '';
	
			$status = _('Posted');
		break;
	}

    $date = new DateTime();
    $date->setTimestamp( $p['date_posted'] - $date->getOffset() + (  $timezone * 3600 ) );

 	$data[] = array(
		$p['post'] . $actions,
		$status,
         $date->format( 'F jS, Y g:i a' )
	);
}

// Send response
echo $dt->get_response( $data );