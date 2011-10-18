<?php
/**
 * @page Website - Upload File
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-file' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );
$ajax->ok( !empty( $_POST['fn'] ) && _('New File Name') != $_POST['fn'], _('You must type in a file name before uploading a file.') );

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );

$file_name = preg_replace( '/[^-_a-zA-Z0-9]/', '', $_POST['fn'] );
$file_name = format::strip_extension( $file_name ) . '.' . $file_extension;
$dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

if ( !is_dir( $dir ) )
	mkdir( $dir, 0777, true );

$file_path = $dir . $file_name;

// User is blank, must fill website
global $user;

// Instantiate class
$w = new Websites;
$wf = new Website_Files;
$ftp = new FTP( $_POST['wid'] );

// Fill the global $user['website']
$user['website'] = $w->get_website( $_POST['wid'] );

// Upload the file
$ajax->ok( move_uploaded_file( $_FILES['Filedata']['tmp_name'], $file_path ), _('An error occurred while trying to upload your file. Please refresh the page and try again.') );

// Transfer file to remote site
$ajax->ok( $ftp->add( $file_path, 'files/' ), _('An error occurred while trying to upload your file to the website. Please refresh the page and try again') );

// Delcare variables
$upload_url = 'http://[domain]/custom/uploads/files/' . $file_name;
$website_file_count = $wf->get_count();

// Add file to database
$ajax->ok( $website_file_id = $wf->add_file( $upload_url ), _('An error occurred while trying to add file to your website. Please refresh the page and try again.') );

// Set it to the proper url
$upload_url = str_replace( '[domain]', ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'], $upload_url );

// If they don't have any files, remove the message that is sitting there
if ( !$website_file_count )
	jQuery('#ulUploadFile li:first')->remove();

// Add the new link and apply sparrow to it
jQuery('#ulUploadFile')
	->append( '<li id="li' . $website_file_id . '"><a href="' . $upload_url . '" id="aFile' . $website_file_id . '" title="' . $file_name . '" class="file">' . $file_name . '</a><a href="/ajax/website/page/delete-file/?_nonce=' . nonce::create('delete-file') . '&amp;wfid=' . $website_file_id . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . _('Are you sure you want to delete this file?') . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a>' )
	->sparrow();

// Adjust back to original name
jQuery('#tFileName')
	->val('')
	->trigger('blur');

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();