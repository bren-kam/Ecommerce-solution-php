<?php
/**
 * @page Website - Upload Video
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-video' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

error_reporting(E_ALL);

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );

$file_name = "video.$file_extension";

// Set variables
$dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

// Directory needs to exist
if ( !is_dir( $dir ) ) {
    // @fix MkDir isnt' changing the permissions, so we have to do the second call too.
	mkdir( $dir, 0777, true );
    chmod( $dir, 0777 );
}

$file_path = $dir . $file_name;

// User is blank, must fill website
global $user;

// Instantiate classes
$w = new Websites;
$wa = new Website_Attachments;
$wf = new Website_Files;
$f = new Files;

// Fill the global $user['website']
$user['website'] = $w->get_website( $_POST['wid'] );

// Upload the file
$ajax->ok( move_uploaded_file( $_FILES['Filedata']['tmp_name'], $file_path ), _('An error occurred while trying to upload your video. Please refresh the page and try again.') );

// Transfer file to Amazon
$ajax->ok( $f->upload_file( $file_path, $file_name, $user['website']['website_id'], 'sidebar/' ), _('An error occurred while trying to upload your video. Please refresh the page and try again') );

// Declare variables
$upload_url = 'http://websites.retailcatalog.us/' . $user['website']['website_id'] . '/sidebar/' . $file_name;

// Update the attachment
$ajax->ok( $wa->update( $_POST['wpid'], 'video', $upload_url ), _('An error occurred while trying to update your video. Please refresh the page and try again.') );

// Add file to database
$ajax->ok( $website_file_id = $wf->add_file( $upload_url ), _('An error occurred while trying to add your video to your website. Please refresh the page and try again.') );

// Delete the file
if ( is_file( $file_path ) )
    unlink( $file_path );

// Add the response
$ajax->add_response( 'refresh', 1 );

// Send response
$ajax->respond();