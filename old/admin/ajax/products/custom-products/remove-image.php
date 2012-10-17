<?php
/**
 * @page Remove Image
 * @package Grey Suit Retail
 */
 
 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'remove-image' );
$ajax->ok( $user, _('You must be signed in to remove a product image.') );

// Instantiate class
$p = new Products;
$f = new Files;

$industry = $p->get_industry( $_GET['pid'] );

$f->delete_image( 'products/' . $product_id . '/' . $_GET['i'], $industry );
$f->delete_image( 'products/' . $product_id . '/thumbnail/' . $_GET['i'], $industry );
$f->delete_image( 'products/' . $product_id . '/large/' . $_GET['i'], $industry );

// Delete image
$ajax->ok( $p->remove_image( $_GET['i'], $_GET['pid'] ), _('An error occurred while trying to remove your product image. Please refresh the page and try again.') );

jQuery('#dProductImage_' . $_GET['i'])
	->remove()
	->updateImageSequence();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();