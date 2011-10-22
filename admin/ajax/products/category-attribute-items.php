<?php
/**
 * @page Category Attribute Items
 * @package Imagine Retailer
 * @subpackage Admin
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'category-attribute-items' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to create a product.') ) );
		exit;
	}
	
	$a = new Attributes;
	
	$attributes = ( empty( $_POST['c'] ) ) ? $a->get_attribute_items() : $a->get_attribute_items_by_categories( $_POST['c'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => true, 'attributes' => $attributes, 'error' => _('An error occurred while trying to create your product. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}