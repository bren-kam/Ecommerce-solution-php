<?php
/**
 * @page Add Comment
 * @package Real Statistics
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-comment' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to add a feedback comment.') ) );
		exit;
	}
	
	// Insantiate classes
	$f = new Feedback;
	$fc = new Feedback_Comments;
	
	// Get feedback information
	$fb = $f->get( $_POST['fid'] );
	
	// Define variables
	$content = stripslashes( $_POST['c'] );
	$status = ( 0 == $fb['status'] ) ? ' (Open)' : ' (Closed)';
	
	// Add it to the comments list
	$result = $fc->add( $_POST['fid'], $user['user_id'], nl2br( $content ) );
	
	// If it's not private, send an email to the client
	if ( '0' == $_POST['p'] )
		fn::mail( $fb['email'], 'Feedback #' . $_POST['fid'] . $status, "******************* Reply Above This Line *******************\n\n$content", 'RealStatistics.com <feedback@realstatistics.com>' );
	
	$feedback_comment = $fc->get_single( $result );
	$feedback_comment['date'] = dt::date( 'm/d/Y g:ia', $feedback_comment['date'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'comment' => $feedback_comment, 'error' => _('An error occurred while trying to add a comment. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}