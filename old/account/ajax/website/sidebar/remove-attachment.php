<?php
/**
 * @page Remove attachment
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'remove-attachment' );
$ajax->ok( $user, _('You must be signed in to remove an attachment.') );

// Instantiate class
$wa = new Website_Attachments();
$wf = new Website_Files();
$f = new Files;

// Get the file
$attachment = $wa->get( $_GET['waid'] );

if ( stristr( $attachment['value'], 'http://' ) ) {
    $file = $wf->get_by_file_path( $attachment['value'] );
	
    // Delete from Amazon S3 (Not checking because it may have been removed other ways )
    $f->delete_file( str_replace( 'http://websites.retailcatalog.us/', '', $file['file_path'] ) );

    // Delete from website
    $ajax->ok( $wf->delete( $file['website_file_id'] ), _('An error occurred while trying to delete your file. Please refresh the page and try again.') );

}

if ( '1' == $_GET['si'] ) {
	// Delete the image
	$ajax->ok( $wa->delete( $_GET['waid'] ), _('An error occurred while trying to delete your website attachment. Please refresh the page and try again.') );
	
	jQuery('#' . $_GET['t'])->remove()->updateDividers();
} else {
	// Empty the value
	$ajax->ok( $wa->update_value( $_GET['waid'], '' ), _('An error occurred while trying to remove your website attachment. Please refresh the page and try again.') );
	
	// Figure out what it's getting replaced with
	switch ( $_GET['t'] ) {
		case 'dRoomPlannerContent':
			$replacement = '<img src="/media/images/placeholders/240x100.png" width="200" height="100" alt="' . _('Placeholder') . '" />';
		break;
		
		case 'dVideoContent':
			$replacement = '<img src="/media/images/placeholders/354x235.png" width="354" height="235" alt="' . _('Placeholder') . '" />';
		break;
		
		default:
			$replacement = '<img src="/media/images/placeholders/240x300.png" width="240" height="300" alt="' . _('Placeholder') . '" />';
		break;
	}
	
	// Replace the current image and remove the remove link
	jQuery('#' . $_GET['t'])->html($replacement);
	jQuery('#aRemove' . $_GET['waid'])->remove();
}

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();