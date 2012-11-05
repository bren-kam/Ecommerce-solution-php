<?php
/**
 * @page Update Priority
 * @package Real Statistics
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-priority' ) ) {
	$f = new Feedback;
	
	$result = $f->update_priority( $_POST['fid'], $_POST['p'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the feedback's priority. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}