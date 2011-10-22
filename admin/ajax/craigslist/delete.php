<?php
/**
 * @page Delete Product
 * @package Real Statistics
 * @subpackage Graphs
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'delete-craigslist' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a craigslist template.') ) );
		exit;
	}
	
	$c = new Craigslist;
	
	$result = $c->delete( $_POST['cid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete the product. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}