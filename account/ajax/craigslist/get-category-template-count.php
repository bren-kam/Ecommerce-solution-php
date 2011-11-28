<?php
/**
 * @page Set Product
 * @package Imagine Retailer
 * @subpackage Account
 */

$ajax = new AJAX( $_POST['_nonce'], 'get-category-template-count' );
$ajax->ok( $user, _('You must be signed in to get a category template count.') );

// Instantiate classes
$c = new Craigslist;

$count = $c->count_templates_for_category( $_POST['cid'] );

if ( !$count ) {
	// no template
	jQuery('#hTemplateCount')->openEditorAndPreview();
} else {
	jQuery('#hTemplateCount')
		->val( $count )
		->openTemplateSelector();
}

// Add the jQuery
$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();