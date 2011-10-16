<?php
/**
 * @page Change Industry
 * @package Imagine Retailer
 */
 
 // Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'change-industry' );
$ajax->ok( $user, _('You must be signed in to change the industry of a product.') );

// Instantiate class
$p = new Products;
	
// Create the product
$ajax->ok( $p->change_industry( $_POST['pid'], $_POST['iid'] ), _('An error occurred while trying to change the industry on your product. Please refresh the page and try again.') );

// Send response
$ajax->respond();