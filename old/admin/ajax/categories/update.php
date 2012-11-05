<?php
/**
 * @page Update Category
 * @package Real Statistics
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-category' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to update a category') ) );
		exit;
	}
		
	$c = new Categories;
	
	if ( $_POST['hCategoryID'] == $_POST['sParentCategory'] ) {
		$cat = $c->get_category( $_GET['hCategoryID'] );
		$parent_category_id = $cat['parent_category_id'];
	} else {
		$parent_category_id = $_POST['sParentCategory'];
	}
	
	$result = $c->update( $_POST['hCategoryID'], $parent_category_id, $_POST['tName'], $_POST['tSlug'], $_POST['hAttributes'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to update category. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}