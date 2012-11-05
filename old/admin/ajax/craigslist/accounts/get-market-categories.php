<?php
/**
 * @page Get Market Categories
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'get-market-categories' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to get market categories.') ) );
		exit;
	}

    // Load the library
    library( 'craigslist-api' );

	// Create API object
    $craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );
    $c = new Craigslist();

    $cl_category_ids = $c->get_cl_category_ids( $_POST['wid'], $_POST['clmid'] );
    $market_categories = $craigslist_api->get_cl_market_categories( $_POST['clmid'] );
	$categories = array();

    if ( is_array( $market_categories ) )
    foreach ( $market_categories as $mc ) {
        if ( in_array( $mc->cl_category_id, $cl_category_ids ) )
            continue;

        $categories[$mc->cl_category_id] = $mc->name;
    }

	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'categories' => $categories, 'error' => _('An error occurred while trying to get your notes. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}