<?php
/**
 * @page Add Comment
 * @package Imagine Retailer
 * @subpackage Admin
 */
 
if( nonce::verify( $_POST['_nonce'], 'add-comment' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to add a ticket comment.') ) );
		exit;
	}
	
	// Insantiate classes
	$t = new Tickets;
	$tc = new Ticket_Comments;
	
	// Get ticket information
	$ticket = $t->get( $_POST['tid'] );
	
	// Define variables
	$content = stripslashes( $_POST['c'] );
	$status = ( 0 == $ticket['status'] ) ? ' (Open)' : ' (Closed)';
	
	// Add it to the comments list
	$result = $tc->add( $_POST['tid'], $user['user_id'], nl2br( htmlentities( $content ) ), $_POST['p'], $_POST['a'] );
	
	// If it's not private, send an email to the client
	if( '0' == $_POST['p'] )
		fn::mail( $ticket['email'], 'Ticket #' . $_POST['tid'] . $status, "******************* Reply Above This Line *******************\n\n{$content}\n\nSupport Issue\n" . $ticket['message'], TICKET . ' <support@' . DOMAIN . '>' );
	
	$ticket_comment = $tc->get_single( $result );
	$ticket_comment['date'] = date_time::date( 'm/d/Y g:ia', $ticket_comment['date'] );
	
	// Send the assigned user an email if they are not submitting the comment
	if( $ticket['assigned_to_user_id'] != $user['user_id'] ) {
		// Get the user
		$assigned_to_user = $u->get_user( $ticket['assigned_to_user_id'] );
		
		// Send email
		fn::mail( $assigned_to_user['email'], 'New Comment on Ticket #' . $_POST['tid'] . ' - ' . $ticket['summary'], $user['contact_name'] . ' has posted a new comment on Ticket #' . $_POST['tid'] . ".\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=" . $_POST['tid'], TITLE . ' <support@' . DOMAIN . '>' );
	}
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'comment' => $ticket_comment, 'error' => _('An error occurred while trying to add a comment. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}