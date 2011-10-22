<?php
/**
 * @page Website - Upload Logo
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-logo' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Instantiate classes
$w = new Websites;
$ftp = new FTP( (int) $_POST['wid'] );

global $user;
$user['website'] = $w->get_website( $_POST['wid'] );

// Set variables
$image_name = $_POST['wid'] . '.' . strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );
$upload_dir = OPERATING_PATH . 'media/uploads/site_logos/';

// Directory needs to exist
if ( !is_dir( $upload_dir ) )
	mkdir( $upload_dir, 0777, true );

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $upload_dir, format::strip_extension( $image_name ), 350, 150 ), _('An error occurred while trying to upload your logo.') );

// Get our local directory
$local_file_path = $upload_dir . $image_name;

// Set the remote directory
$remote_directory = 'images/';

// Add it to their site
$ajax->ok( $ftp->add( $local_file_path, $remote_directory ), _('An error occurred while trying to upload your logo') );

// Create the upload url url
$upload_url = '/custom/uploads/' . $remote_directory . $image_name;

// Update the top section
$w->update( array( 'logo' => $image_name ), 's' ); 

unlink( $local_file_path ); // delete file

// @Fix Since we have the image we could probably do a getimagesize without the slow down
jQuery('#dLogoContent')->html('<img src="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $upload_url . '" style="padding-bottom:10px" alt="' . _('Logo') . '" /><br />' );

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();