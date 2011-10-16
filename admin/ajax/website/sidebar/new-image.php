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

$name = format::slug( format::strip_extension( $_FILES["Filedata"]['name'] ) );

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

// Set the website data, if successful, delete the local file
$ajax->ok( $website_attachment_id = $wa->create( $_POST['wpid'], 'sidebar-image', $upload_url ), _('An error occurred while trying to upload your image. Please refresh the page and try again.') );

// Delete the file
unlink( $local_file_path );
		
$contact_box = '<div class="contact-box" id="dAttachment_' . $website_attachment_id . '">';
$contact_box .= '<h2>' . _('Sidebar Image') . '</h2>';
$contact_box .= '<a href="/ajax/website/sidebar/update-status/?_nonce=' . nonce::create( 'update-status' ) . '&amp;waid=' . $website_attachment_id . '&amp;s=0" id="aEnableDisable' . $website_attachment_id . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.') . '"><img src="/images/trans.gif" width="26" height="28" alt="' . _('Enable/Disable') . '" /></a>';
$contact_box .= '<div id="dSidebarImage' . $website_attachment_id . '"><br />';
$contact_box .= '<form action="/ajax/website/sidebar/update-extra/" method="post" ajax="1">';
$contact_box .= '<div align="center">';
$contact_box .= '<p><img src="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $upload_url . '" alt="' . _('Sidebar Image') . '" /></p>';
$contact_box .= '<p><a href="/ajax/website/sidebar/remove-attachment/?_nonce=' . nonce::create('remove-attachment') . '&amp;waid=' . $website_attachment_id . '&amp;t=dAttachment_' . $website_attachment_id . '&amp;si=1" id="aRemove' . $website_attachment_id . '" title="' . _('Remove Image') . '" ajax="1" confirm="' . _('Are you sure you want to remove this sidebar element?') . '">' . _('Remove') . '</a></p>';
$contact_box .= '<p><input type="text" class="tb" name="extra" id="tSidebarImage' . $website_attachment_id . '" tmpval="' . _('Enter Link...') . '" value="http://" /></p>';
$contact_box .= '<p id="pTempSidebarImage' . $website_attachment_id . '" class="success hidden">' . _('Your Sidebar Image link has been successfully updated.') . '</p><br />';
$contact_box .= '<p align="center"><input type="submit" class="button" value="' . _('Save') . '" /></p>';
$contact_box .= '</div>';
$contact_box .= '<input type="hidden" name="hWebsiteAttachmentID" value="' . $website_attachment_id . '" />';
$contact_box .= '<input type="hidden" name="target" value="pTempSidebarImage' . $website_attachment_id . '" />';
$contact_box .= nonce::field( 'update-extra', '_ajax_update_extra', false );
$contact_box .= '</form></div></div>';

jQuery('#dContactBoxes')
	->append( $contact_box )
	->updateElementOrder()
	->updateDividers()
	->sparrow();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();