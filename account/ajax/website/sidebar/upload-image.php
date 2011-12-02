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

$file_name = $_POST['name'];
$image_name = "$file_name.$file_extension";

$dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

if ( !is_dir( $dir ) )
	mkdir( $dir, 0777, true );

$file_path = $dir . $image_name;

// User is blank, must fill website
global $user;

// Instantiate classes
$w = new Websites;

$user['website'] = $w->get_website( $_POST['wid'] );

// Instantiate other classes now that ther user['website'] is in place
$wa = new Website_Attachments;
$f = new Files;

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $dir, $name, 1000, 1000 ), _('An error occurred while trying to upload your image.') );

// Transfer file to Amazon
$ajax->ok( $f->upload_file( $file_path, $image_name, $user['website']['website_id'], 'sidebar/' ), _('An error occurred while trying to upload your image. Please refresh the page and try again') );

// Declare variables
$upload_url = 'http://websites.retailcatalog.us/' . $user['website']['website_id'] . '/sidebar/' . $image_name;

// Delete the file
if ( is_file( $file_path ) )
    unlink( $file_path );

echo true;
return true;