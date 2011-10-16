<?php
/**
 * @page Update Feedback Status
 * @package Real Statistics
 */
 
if( nonce::verify( $_POST['_nonce'], 'update-feedback-status' ) ) {
	$f = new Feedback;
	
	$result = $f->update_status( $_POST['fid'], $_POST['s'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the feedback's status. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}