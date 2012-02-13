<?php
/**
 * @page Add Comment
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
$ajax = new AJAX( $_POST['_nonce'], 'add-comment' );
$ajax->ok( $user, _('You must be signed in to add a reach comment.' ) );

// Insantiate classes
$r = new Reaches;
$rc = new Reach_Comments;
	
// Get reach information
$reach = $r->get( $_POST['rid'] );
	
// Define variables
$content = stripslashes( $_POST['taReachComment'] );
$status = ( 0 == $reach['status'] ) ? ' (Open)' : ' (Closed)';
$private = ( $_POST['cbPrivate'] ) ? 1 : 0;
	
// Add it to the comments list
$result = $rc->add( $_POST['rid'], 0, $user['user_id'], nl2br( htmlentities( $content ) ), $private, $_POST['a'] );

$ajax->ok( $result, _('There was an error adding your comment.') );
	
// If it's not private, send an email to the client
//if ( $private )
	// TODO fn::mail( $ticket['email'], 'Ticket #' . $_POST['tid'] . $status, "******************* Reply Above This Line *******************\n\n{$content}\n\nSupport Issue\n" . $ticket['message'], TICKET . ' <support@' . DOMAIN . '>' );
	
$reach_comment = $rc->get_single( $result );
$reach_comment['date'] = dt::date( 'm/d/Y g:ia', $reach_comment['date'] );

// Send the assigned user an email if they are not submitting the comment
if ( $reach['assigned_to_user_id'] != $user['user_id'] ) {
	// Get the user
	$assigned_to_user = $u->get_user( $reach['assigned_to_user_id'] );
	
	// TODO Send email
	//fn::mail( $assigned_to_user['email'], 'New Comment on Ticket #' . $_POST['tid'] . ' - ' . $ticket['summary'], $user['contact_name'] . ' has posted a new comment on Ticket #' . $_POST['tid'] . ".\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=" . $_POST['tid'], TITLE . ' <support@' . DOMAIN . '>' );
}

// TODO add comment to page via jQuery
	
$ajax->respond();
