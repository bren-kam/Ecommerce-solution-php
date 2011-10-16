<?php
/**
 * @page Website - Upload Image
 * @package Imagine Retailer
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-coupon' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );

// Instantiate classes
$w = new Websites;
$wa = new Website_Attachments;

global $user;
$user['website'] = $w->get_website( $_POST['wid'] );

// Variables
$page = $w->get_page( $_POST['wpid'] );

switch ( $page['slug'] ) {
	case 'about-us':
		$name = $key = 'about-us';
		$key = 'apply-now';
		$width = 400;
		$height = 350;
	break;
	
	case 'current-offer':
		$name = $key = 'coupon';
		$width = 405;
		$height = 245;
	break;
	
	case 'financing':
		$name = 'btn.apply-now';
		$key = 'apply-now';
		$width = 200;
		$height = 70;
	break;
}

// Set variables
$image_name = "$name.$file_extension";
$upload_url = 'http://account2.' . DOMAIN . '/media/uploads/site_uploads/' . $_POST['wid'] . "/$image_name";
$upload_dir = OPERATING_PATH . 'media/uploads/site_uploads/' . $_POST['wid'] . '/';

// Directory needs to exist
if( !is_dir( $upload_dir ) )
	mkdir( $upload_dir, 0777, true );

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $upload_dir, $name, $width, $height ), _('An error occurred while trying to upload your image. Please refresh the page and try again.') );

// Update the request attachment
$wa->update( $_POST['wpid'], $key, $upload_url );

switch( $page['slug'] ) {
	case 'about-us':
		jQuery('#dAboutUsContent')->html('<img src="' . $upload_url . '" style="padding-bottom:10px" alt="' . _('About Us') . '" /><br />');
	break;
	
	case 'current-offer':
		jQuery('#dCouponContent')->html('<img src="' . $upload_url . '" style="padding-bottom:20px" alt="' . _('Coupon') . '" /><br />');
	break;
	
	case 'financing':
		jQuery('#dApplyNowContent')->html('<img src="' . $upload_url . '" style="padding-bottom:10px" alt="' . _('Apply Now') . '" /><br /><p>' . _('Place "[apply-now]" into the page content above to place the location of your image. When you view your website, this will be replaced with the image uploaded.') . '</p>' );
	break;
}

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();