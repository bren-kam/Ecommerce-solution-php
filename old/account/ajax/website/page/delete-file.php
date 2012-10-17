<?php
/**
 * @page Delete File
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-file' );
$ajax->ok( $user, _('You must be signed in to delete a file.') );

// Instantiate class
$wf = new Website_Files();
$f = new Files;

// Type Juggling
$website_file_id = (int) $_GET['wfid'];

// Get the file
$file = $wf->get_by_id( $website_file_id );

// Delete from Amazon S3
$ajax->ok( $f->delete_file( str_replace( 'http://websites.retailcatalog.us/', '', $file['file_path'] ) ), _('An error occurred while trying to delete your file from the server. Please refresh the page and try again.') . str_replace( 'http://websites.retailcatalog.us/', '', $file['file_path'] ) );

// Delete from website
$ajax->ok( $wf->delete( $website_file_id ), _('An error occurred while trying to delete your file. Please refresh the page and try again.') );

// Remove that li
jQuery('#li' . $website_file_id)->remove();

// Get the files, see how many there are
if ( !$wf->get_count() )
	jQuery('#ulUploadFile')->append('<li>', _('You have not uploaded any files.') . '</li>'); // Add a message

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();