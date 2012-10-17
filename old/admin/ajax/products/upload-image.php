<?php
/**
 * @page Upload Image
 * @package Grey Suit Retail
 * @subpackage Admin
 */

/**
 * @page Upload Video
 * @package Rethink Trainig
 */
if ( !empty( $_FILES ) && nonce::verify( $_POST['_nonce'], 'upload-image' ) ) {
	$product_id = (int) $_POST['pid'];
	$new_image_name = strtolower( preg_replace( array( '/[^-a-zA-Z0-9\s]/', '/[\s]/' ), array( '', '-' ), f::strip_extension( $_FILES["Filedata"]['name'] ) ) );
	$image_extension = strtolower( f::extension( $_FILES["Filedata"]['name'] ) );
	
	// Instantiate file-handling class
	$f = new Files;
	$i = new Industries;
	$industry = $i->get_by_product( $product_id );
	
	// Remove spaces
	$industry = str_replace( " ", "", $industry );

	$f->upload_image( $_FILES["Filedata"], $new_image_name, 320, 320, $industry, 'products/' . $product_id . '/', false, true );
	$f->upload_image( $_FILES["Filedata"], $new_image_name, 46, 46, $industry, 'products/' . $product_id . '/thumbnail/', false, true );
	$f->upload_image( $_FILES["Filedata"], $new_image_name, 200, 200, $industry, 'products/' . $product_id . '/small/', false, true );
	$f->upload_image( $_FILES["Filedata"], $new_image_name, 700, 700, $industry, 'products/' . $product_id . '/large/', false, true );

	// Upload the video
	echo 'http://' . $industry . '.retailcatalog.us/products/' . $product_id . '/thumbnail/' . $new_image_name . '.' . $image_extension . '|' . '/' . $new_image_name . '.' . $image_extension;
}