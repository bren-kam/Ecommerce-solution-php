<?php
/**
 * @page Website - Upload Banner
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-banner' );
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
$banner_name = "$name.$file_extension";
$upload_dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

// Directory needs to exist
if ( !is_dir( $upload_dir ) )
	mkdir( $upload_dir, 0777, true );

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $upload_dir, $name, 1000, 1000 ), _('An error occurred while trying to upload your banner.') );

// Get our local directory
$local_file_path = $upload_dir . $banner_name;

$remote_directory = 'slideshow/';

// Get the path info
$pathinfo = pathinfo( $local_file_path );

// Add it to their site
$ajax->ok( $ftp->add( $local_file_path, $remote_directory ), _('An error occurred while trying to upload the banner to your site') );

// Create the absolute url
$absolute_url = '/custom/uploads/' . $remote_directory . $pathinfo['basename'];

// Set the website data, if successful, delete the local file
$ajax->ok( $website_attachment_id = $wa->create( $_POST['wpid'], 'banner', $absolute_url ), _('An error occurred while trying to upload your banner. Please refresh the page and try again.') );

unlink( $local_file_path ); // Delete file
		
$settings = $w->get_settings('banner-width');

$contact_box = '<div class="contact-box" id="dAttachment_' . $website_attachment_id . '" style="width:' . $settings['banner-width'] . 'px">';
$contact_box .= '<h2>' . _('Flash Banner') . '</h2>';
$contact_box .= '<p><small>' . $settings['banner-width'] . '</small></p>';
$contact_box .= '<a href="/ajax/website/sidebar/update-status/?_nonce=' . nonce::create( 'update-status' ) . '&amp;waid=' . $website_attachment_id . '&amp;s=0" id="aEnableDisable' . $website_attachment_id . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this banner?') . '"><img src="/images/trans.gif" width="76" height="25" alt="' . _('Enable/Disable') . '" /></a>';
$contact_box .= '<div id="dBanner' . $website_attachment_id . '" class="text-center">';
$contact_box .= '<img src="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $absolute_url . '" alt="' . _('Sidebar Image') . '" />';
$contact_box .= '</div><br />';
$contact_box .= '<form action="/ajax/website/sidebar/update-extra/" method="post" ajax="1">';
$contact_box .= '<p id="pTempSuccess' . $website_attachment_id . '" class="success hidden">' . _('Your banner link has been successfully updated.') . '</p>';
$contact_box .= '<p><input type="text" class="tb" name="extra" id="tSidebarImage' . $website_attachment_id . '" tmpval="' . _('Enter Link...') . '" value="http://" /></p>';
$contact_box .= '<input type="submit" class="button" value="' . _('Save') . '" />';
$contact_box .= '<input type="hidden" name="hWebsiteAttachmentID" value="' . $website_attachment_id . '" />';
$contact_box .= '<input type="hidden" target="hWebsiteAttachmentID" value="pTempSuccess' . $website_attachment_id . '" />';
$contact_box .= nonce::field( 'update-extra', '_ajax_update_extra', false );
$contact_box .= '</form>';
$contact_box .= '<a href="/ajax/website/sidebar/remove-attachment/?_nonce=' . nonce::create('remove-attachment') . '&amp;waid=' . $website_attachment_id . '&amp;t=dAttachment_' . $website_attachment_id . '&amp;si=1" class="remove" title="' . _('Remove Banner') . '" ajax="1" confirm="' . _('Are you sure you want to remove this banner?') . '">' . _('Remove') . '</a></p>';
$contact_box .= '<br clear="all" /></div>';

jQuery('#dContactBoxes')
	->append( $contact_box )
	->updateElementOrder()
	->updateDividers()
	->sparrow();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();