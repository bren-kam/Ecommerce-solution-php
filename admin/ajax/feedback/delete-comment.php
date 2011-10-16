<?php
/**
 * @page Delete Comment
 * @package Real Statistics
 * @subpackage Graphs
 */
 
if( nonce::verify( $_POST['_nonce'], 'delete-comment' ) ) {
	if( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a feedback comment.') ) );
		exit;
	}
	
	$fc = new Feedback_Comments;
	
	$result = $fc->delete( $_POST['fcid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete the feedback comment. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}