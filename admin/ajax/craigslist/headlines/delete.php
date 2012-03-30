<?php
/**
 * @page Delete Craigslist Headline
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'delete-craigslist-headline' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete a craigslist headline.') ) );
		exit;
	}
	
	$c = new Craigslist;
	
	$result = $c->delete_headline( $_POST['chid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete the headline. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}