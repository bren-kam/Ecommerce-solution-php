<?php
/**
 * @page Website - New Image
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'new-image' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );

global $user;

// Instantiate classes
$w = new Websites;

$user['website'] = $w->get_website( $_POST['wid'] );

// Instantiate other classes now that ther user['website'] is in place
$wa = new Website_Attachments;
$ftp = new FTP( (int) $_POST['wid'] );

$name = $_POST['name'];

// Set variables
$image_name = "$name.$file_extension";
$upload_dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

// Directory needs to exist
if( !is_dir( $upload_dir ) )
	mkdir( $upload_dir, 0777, true );

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $upload_dir, $name, 1000, 1000 ), _('An error occurred while trying to upload your image.') );

// Get our local directory
$local_file_path = $upload_dir . $image_name;

// Add it to their site
$ajax->ok( $ftp->add( $local_file_path, 'images/' ), _('An error occurred while trying to upload your image. Please refresh the page and try again.') );

// Create the upload url
$upload_url = '/custom/uploads/images/' . $image_name;

// (Not needed for this one, since we don't want to create new files) Set the website data, if successful, delete the local file
// $ajax->ok( $website_attachment_id = $wa->create( $_POST['wpid'], 'sidebar-image', $upload_url ), _('An error occurred while trying to upload your image. Please refresh the page and try again.') );

// Delete the file
unlink( $local_file_path );

// $new_image = "<img src='http://" . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $upload_url . "' alt='" . _('Current Ad Image') . "' />";
// $replace_id = '#' . $_POST['replace_id'];

echo true;
return true;

//@Fix nothing below here is working.  WTF?
/*
jQuery( $replace_id ) //$replace_id )
	->replace( $new_image );
	//->sparrow();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();
*/