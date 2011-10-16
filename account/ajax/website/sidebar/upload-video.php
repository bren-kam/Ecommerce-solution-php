<?php
/**
 * @page Website - Upload Video
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-video' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );


// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );

global $user;

// Instantiate classes
$w = new Websites;
$wa = new Website_Attachments;

$user['website'] = $w->get_website( $_POST['wid'] );

// Instantiate other classes now that ther user['website'] is in place
$ftp = new FTP( (int) $_POST['wid'] );

$name = "video.$file_extension";

// Set variables
$upload_dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] ;
$local_file_path = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . "/$name";

// Directory needs to exist
if( !is_dir( $upload_dir ) )
	mkdir( $upload_dir, 0777, true );

// Move it to a local file
$ajax->ok( move_uploaded_file( $_FILES['Filedata']['tmp_name'], $local_file_path ) );

// Add it to their site
$ajax->ok( $ftp->add( $local_file_path, 'video/' ), _('An error occurred while trying to upload your video. Please refresh the page and try again.') );
	
// Update the video
// No Workee --> $ajax->ok( $wa->update( $_POST['wpid'], 'video', 'http://' . $user['website']['domain'] . '/custom/uploads/video/' . $name ), _('An error occurred while trying to update your video. Please refresh the page and try again.') );
$ajax->ok( $wa->update( $_POST['wpid'], 'video', '/custom/uploads/video/' . $name ), _('An error occurred while trying to update your video. Please refresh the page and try again.') );

// Delete the file
unlink( $local_file_path );

// Add the response
$ajax->add_response( 'refresh', 1 );

// Send response
$ajax->respond();