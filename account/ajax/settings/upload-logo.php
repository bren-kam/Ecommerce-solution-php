<?php
/**
 * @page Settings - Upload Logo
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-logo' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );
$image_name = "logo." . str_replace( 'jpeg', 'jpg', $file_extension );

$dir = OPERATING_PATH . 'media/uploads/site_logos/' . $_POST['wid'] . '/';

// Directory needs to exist
if ( !is_dir( $upload_dir ) )
	mkdir( $upload_dir, 0777, true );

$image_path = $dir . $image_name;

// User is blank, must fill website
global $user;

// Instantiate classes
$w = new Websites;
$wf = new Website_Files;
$f = new Files;

$user['website'] = $w->get_website( $_POST['wid'] );

// Resize the logo
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $dir, 'logo', 700, 200, true, false ), _('An error occurred while trying to upload your logo. Please refresh the page and try again.') );
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $dir . 'large/', 'logo', 700, 700, 100, true, false ), _('An error occurred while trying to upload your logo. Please refresh the page and try again.') );

// Transfer file to Amazon
$ajax->ok( $f->upload_file( $image_path, $image_name, $user['website']['website_id'], 'logo/' ), _('An error occurred while trying to upload your logo to the website. Please refresh the page and try again') );
$ajax->ok( $f->upload_file( $dir . 'large/' . $image_name, $image_name, $user['website']['website_id'], 'logo/large/' ), _('An error occurred while trying to upload your logo (large) to the website. Please refresh the page and try again') );

// Declare variables
$upload_url = 'http://websites.retailcatalog.us/' . $user['website']['website_id'] . '/logo/' . $image_name;

// Add file to database
$ajax->ok( $website_file_id = $wf->add_file( $upload_url ), _('An error occurred while trying to add file to your website. Please refresh the page and try again.') );

// Delete the file
if ( is_file( $image_path ) )
    unlink( $image_path );

// Delete the dimensions of the old logo
$w->delete_image_dimensions( $user['website']['logo'] );

// Update the top section
$w->update( array( 'logo' => $upload_url ), 's' );

// @Fix Since we have the image we could probably do a getimagesize without the slow down
jQuery('#dLogoContent')->html('<img src="' . $upload_url . '" style="padding-bottom:10px" alt="' . _('Logo') . '" /><br />' );

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();