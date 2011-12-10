<?php
/**
 * @page List Posting posts
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$s = new Social_Media();
$dt = new Data_Table();

// Set variables
$dt->order_by( '`post`', '`status`', '`date_posted`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`post`' => true ) );

// Get autoresponder
$posts = $s->list_posting_posts( $dt->get_variables() );
$dt->set_row_count( $s->count_posting_posts( $dt->get_where() ) );

$confirm = _('Are you sure you want to cancel this post? This cannot be undone.');
$delete_post_nonce = nonce::create( 'delete-post' );

// Create output
if ( is_array( $posts ) )
foreach ( $posts as $p ) {
	$actions = ( 1 == $p['status'] ) ? '' : ;

    // Determine what to do based off the status
    if ( 1 == $p['status'] ) {
        $actions = '';

        $status = _('Posted');
    } else {
        $actions = '<br />
        <div class="actions">
            <a href="/ajax/social-media/facebook/posting/delete/?sppid=' . $p['sm_posting_post_id'] . '&amp;_nonce=' . $delete_post_nonce . '" title="' . _('Delete Post') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
        </div>';

        $status = _('Scheduled');
    }

    $date = new DateTime( $p['date_posted'] );

 	$data[] = array(
		format::limit_chars( $p['post'], 100 ) . $actions,
		$status,
         $date->format( 'F jS, Y g:i a' )
	);
}

// Send response
echo $dt->get_response( $data );