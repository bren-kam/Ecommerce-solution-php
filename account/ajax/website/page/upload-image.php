<?php
/**
 * @page Website - Upload Image
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-image' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Get the file extension
$file_extension = strtolower( format::file_extension( $_FILES["Filedata"]['name'] ) );
$dir = OPERATING_PATH . 'media/uploads/site_logos/' . $_POST['wid'] . '/';

// Directory needs to exist
if ( !is_dir( $dir ) ) {
    // @fix MkDir isnt' changing the permissions, so we have to do the second call too.
	mkdir( $dir, 0777, true );
    chmod( $dir, 0777 );
}

// User is blank, must fill website
global $user;

// Instantiate classes
$w = new Websites;
$wa = new Website_Attachments;
$f = new Files;

// Variables
$user['website'] = $w->get_website( $_POST['wid'] );
$page = $w->get_page( $_POST['wpid'] );

switch ( $page['slug'] ) {
	case 'about-us':
		$name = $key = 'about-us';
		$width = 400;
		$height = 350;
	break;

    default:
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
$image_name = "$name." . str_replace( 'jpeg', 'jpg', $file_extension );
$image_path = $dir . $image_name;

// Resize the image
$ajax->ok( image::resize( $_FILES["Filedata"]['tmp_name'], $dir, $name, $width, $height ), _('An error occurred while trying to upload your image. Please refresh the page and try again.') );

// Transfer file to Amazon
$ajax->ok( $f->upload_file( $image_path, $image_name, $user['website']['website_id'], "$key/" ), _('An error occurred while trying to upload your logo to the website. Please refresh the page and try again') );

// Set variables
$upload_url = 'http://websites.retailcatalog.us/' . $user['website']['website_id'] . "/$key/" . $image_name;

// Update the request attachment, or creates one if it doesn't exist
if ( $wa->get_by_name( $_POST['wpid'], $key ) ) {
	$wa->update( $_POST['wpid'], $key, $upload_url );
} else {
	$wa->create( $_POST['wpid'], $key, $upload_url );
}

switch ( $page['slug'] ) {
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