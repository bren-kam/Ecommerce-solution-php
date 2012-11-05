<?php
/**
 * @page Add Comment
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-comment' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to add an issue comment.') ) );
		exit;
	}
	
	// Insantiate classes
	$i = new Issues;
	
	// Define variables
	$content = stripslashes( $_POST['c'] );
	
	// Add it to the comments list
	$result = $i->add_comment( $_POST['ik'], $user['user_id'], nl2br( htmlentities( $content ) ) );
	
	$issue_comment = $i->get_comment( $result );
	$issue_comment['date'] = dt::date( 'm/d/Y g:ia', $issue_comment['date'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'comment' => $issue_comment, 'error' => _('An error occurred while trying to add a comment. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}