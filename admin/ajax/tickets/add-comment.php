<?php
/**
 * @page Add Comment
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-comment' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to add a ticket comment.') ) );
		exit;
	}
	
	// Insantiate classes
	$t = new Tickets;
	$tc = new Ticket_Comments;
	
	// Get ticket information
	$ticket = $t->get( $_POST['tid'] );
	
	// Get the user
	$assigned_to_user = $u->get_user( $ticket['assigned_to_user_id'] );

	// Define variables
	$content = stripslashes( $_POST['c'] );
	$status = ( 0 == $ticket['status'] ) ? ' (Open)' : ' (Closed)';
	
	// Add it to the comments list
	$result = $tc->add( $_POST['tid'], $user['user_id'], nl2br( format::links_to_anchors( htmlentities( $content ), true, true ) ), $_POST['p'], $_POST['a'] );
	
	// If it's not private, send an email to the client
	if ( '0' == $_POST['p'] && 1 == $ticket['status'] )
		fn::mail( $ticket['email'], 'Ticket #' . $_POST['tid'] . $status, "******************* Reply Above This Line *******************\n\n{$content}\n\nSupport Issue\n" . $ticket['message'], $assigned_to_user['company'] . ' <support@' . $assigned_to_user['domain'] . '>' );
	
	$ticket_comment = $tc->get_single( $result );
	$ticket_comment['date'] = dt::date( 'm/d/Y g:ia', $ticket_comment['date'] );
	
	// Send the assigned user an email if they are not submitting the comment
	if ( $ticket['assigned_to_user_id'] != $user['user_id'] && 1 == $ticket['status'] )
		fn::mail( $assigned_to_user['email'], 'New Comment on Ticket #' . $_POST['tid'] . ' - ' . $ticket['summary'], $user['contact_name'] . ' has posted a new comment on Ticket #' . $_POST['tid'] . ".\n\nhttp://admin." . $assigned_to_user['domain'] . "/tickets/ticket/?tid=" . $_POST['tid'], $assigned_to_user['company'] . ' <support@' . $assigned_to_user['domain'] . '>' );

	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'comment' => $ticket_comment, 'error' => _('An error occurred while trying to add a comment. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}