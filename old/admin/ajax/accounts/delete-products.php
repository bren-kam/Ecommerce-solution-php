<?php
/**
 * @page Edit Website
 * @package Grey Suit Retail
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'delete-products' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete the categories and products from a website.') ) );
		exit;
	}

    // Make sure they have permission to remove it
    if ( $user['role'] < 7 ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to delete the categories and products from a website.') ) );
		exit;
	}


    $w = new Websites;
    $result = $w->delete_categories_and_products( $_POST['wid'] );

	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to delete the categories and products from a website. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}