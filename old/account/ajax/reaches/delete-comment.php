<?php
/**
 * @page Delete Comment
 * @package Grey Suit Retail
 * @subpackage Graphs
 */
 
$ajax = new AJAX( $_GET['_nonce'], 'delete-comment' );
$ajax->ok( $user['user_id'], _('You must be signed in to delete a comment') );

// create class
$rc = new Reach_Comments;
$comment = $rc->get_single( $_GET['rcid'] );

// Check if its their comment
$ajax->ok( $comment['user_id'] == $user['user_id'], _('You must be logged in as this user to delete his or her comment.') );
$ajax->ok( $result = $rc->delete( $_GET['rcid'] ) );

// Remove comment from list
jQuery( "#dComment" . $_GET['rcid'] )
	->remove();
	
$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();
