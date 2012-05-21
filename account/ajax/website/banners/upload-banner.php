<?php
/**
 * @page Website - Upload Banner
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-banner' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );
$dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

// Directory needs to exist
if ( !is_dir( $dir ) )
	mkdir( $dir, 0777, true );

// User is blank, must fill website
global $user;

// Instantiate classes
$w = new Websites;
$wa = new Website_Attachments;
$f = new Files;

// Variables
$user['website'] = $w->get_website( $_POST['wid'] );

// Set variables
$name = format::slug( format::strip_extension( $_FILES["Filedata"]['name'] ) );
$banner_name = "$name.$file_extension";
$banner_path = $dir . $banner_name;
$settings = $w->get_settings( 'banner-width', 'banner-height' );

$max_width = ( empty ( $settings['banner-width'] ) ) ? 1000 : $settings['banner-width'];
$max_height = ( empty ( $settings['banner-height'] ) ) ? 1000 : $settings['banner-height'];

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $dir, $name, $max_width, $max_height ), _('An error occurred while trying to upload your banner. Please refresh the page and try again.') );

// Transfer file to Amazon
$ajax->ok( $f->upload_file( $banner_path, $banner_name, $user['website']['website_id'], "banners/" ), _('An error occurred while trying to upload your logo to the website. Please refresh the page and try again') );

// Set variables
$upload_url = 'http://websites.retailcatalog.us/' . $user['website']['website_id'] . "/banners/" . $banner_name;

// Set the website data, if successful, delete the local file
$ajax->ok( $website_attachment_id = $wa->create( $_POST['wpid'], 'banner', $upload_url ), _('An error occurred while trying to upload your banner. Please refresh the page and try again.') );

unlink( $banner_path ); // Delete file

$settings = $w->get_settings( 'banner-width', 'banner-height' );

$contact_box = '<div class="contact-box" id="dAttachment_' . $website_attachment_id . '" style="width:' . $settings['banner-width'] . 'px">';
$contact_box .= '<h2>' . _('Banner') . '</h2>';
$contact_box .= '<p><small>' . $settings['banner-width'] . 'x' . $settings['banner-height'] . '</small></p>';
$contact_box .= '<a href="/ajax/website/sidebar/update-status/?_nonce=' . nonce::create( 'update-status' ) . '&amp;waid=' . $website_attachment_id . '&amp;s=0" id="aEnableDisable' . $website_attachment_id . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this banner?') . '"><img src="/images/trans.gif" width="76" height="25" alt="' . _('Enable/Disable') . '" /></a>';
$contact_box .= '<div id="dBanner' . $website_attachment_id . '" class="text-center">';
$contact_box .= '<img src="' . $upload_url . '" alt="' . _('Banner Image') . '" />';
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