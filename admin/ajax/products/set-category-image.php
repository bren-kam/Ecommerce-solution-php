<?php
/**
 * @page Set Category Image
 * @package Imagine Retailer
 */

 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'set-category-image' );
$ajax->ok( $user, _('You must be signed in to set a category image.') );

$wc = new Website_Categories;

// Delete the product
$ajax->ok( $wc->set_category_image( $_GET['cid'], $_GET['i'] ), _('An error occurred while trying to set your category image. Please refresh the page and try again.') );

// Send response
$ajax->respond();