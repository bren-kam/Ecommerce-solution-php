<?php
/**
 * @page Update a Status (Enable/Disable)
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'update-status' );
$ajax->ok( $user, _('You must be signed in to access this feature.') );

// Instantiate class
$wa = new Website_Attachments();

// Make sure it updated successfully
$ajax->ok( $wa->update_status( $_GET['waid'], $_GET['s'] ), _('An error occurred while trying to perform your request. Please refresh the page and try again.') );

$update_status_nonce = nonce::create( 'update-status' );

if ( '0' == $_GET['s'] ) {
	// Disabled
	jQuery('#aEnableDisable' . $_GET['waid'])->replaceWith('<a href="/ajax/website/sidebar/update-status/?_nonce=' . $update_status_nonce . '&amp;waid=' . $_GET['waid'] . '&amp;s=1" id="aEnableDisable' . $_GET['waid'] . '" class="enable-disable disabled" title="' . _('Enable/Disable') . '" ajax="1"><img src="/images/trans.gif" width="26" height="28" alt="' . _('Enable/Disable') . '" /></a>')->sparrow();
	jQuery('#dAttachment_' . $_GET['waid'])
		->addClass('disabled')
		->insertAfter('#dContactBoxes .contact-box:last:not(#dAttachment_' . $_GET['waid'] . ')')
		->updateElementOrder()
		->updateDividers();
} else {
	// Enabled
	jQuery('#aEnableDisable' . $_GET['waid'])->replaceWith('<a href="/ajax/website/sidebar/update-status/?_nonce=' . $update_status_nonce . '&amp;waid=' . $_GET['waid'] . '&amp;s=0" id="aEnableDisable' . $_GET['waid'] . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.') . '"><img src="/images/trans.gif" width="26" height="28" alt="' . _('Enable/Disable') . '" /></a>')->sparrow();
	jQuery('#dAttachment_' . $_GET['waid'])->removeClass('disabled');
}

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();