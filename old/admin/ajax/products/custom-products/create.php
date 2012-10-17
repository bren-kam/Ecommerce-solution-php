<?php
/**
 * @page Create Custom Product
 * @package Grey Suit Retail
 */
 
 // Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'create-custom-product' );
$ajax->ok( $user, _('You must be signed in to create a custom product.') );

// Instantiate class
$p = new Products;
	
// Create the product
$ajax->ok( $product_id = $p->create(), _('An error occurred while trying to create your product. Please refresh the page and try again.') );

// Add the jQuery
jQuery('#hProductID')->val( $product_id );
jQuery('#fUploadImages')->uploadifySettings( 'scriptData', array( '_nonce' => nonce::create('upload-image'), 'pid' => $product_id, 'wid' => $user['website']['website_id'] ) );
jQuery('#fAddEdit')->attr( 'action', "/products/custom-products/add-edit/?pid=$product_id" );

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Botta bing, botta boom
$ajax->respond();