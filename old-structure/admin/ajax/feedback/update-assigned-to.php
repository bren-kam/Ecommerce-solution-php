<?php
/**
 * @page Update Feedback Status
 * @package Real Statistics
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-assigned-to' ) ) {
	$f = new Feedback;
	
	$result = $f->update_assigned_to( $_POST['fid'], $_POST['atui'] );
	
	$new_user = $u->get_user( (int) $_POST['atui'] );
	
	$message = 'Hello ' . $new_user['first_name'] . ",\n\n";
	$message .= 'You have been assigned Feedback #' . $_POST['fid'] . ". To view it, follow the link below:\n\n";
	$message .= 'http://admin.realstatistics.com/view-feedback/?fid=' . $_POST['fid'] . "\n\n";
	$message .= "Sincerely,\nRealStatistics.com Team";
	
	fn::mail( $new_user['email'], 'You have beeen assigned Feedback #' . $_POST['fid'], $message );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _("An error occurred while trying to update the feedback's assigned to. Please refresh the page and try again.") ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}