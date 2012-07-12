<?php
/**
 * @page Get Categories
 * @package Real Statistics
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'get-categories' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to get categories.') ) );
		exit;
	}
	
	$c = new Categories;
	
	$categories = $c->get_child_categories( $_POST['cid'] );
	$parent_category = $c->get_category( $_POST['cid'] );
	$breadcrumb = $c->get_chain( $_POST['cid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'categories' => $categories, 'parent_category' => $parent_category, 'breadcrumb' => $breadcrumb, 'error' => _('An error occurred while trying to get categories. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}