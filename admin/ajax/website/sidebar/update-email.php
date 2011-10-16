<?php
/**
 * @page Update email
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_ajax_update_email'], 'update-email' );
$ajax->ok( $user, _('You must be signed in to update your sidebar email.') );

// Instantiate class
$wa = new Website_Attachments();

// Make sure it updated successfully
$ajax->ok( $wa->update_value( $_POST['hWebsiteAttachmentID'], $_POST['taEmail'] ), _('An error occurred while trying to update the sequence of your sidebar. Please refresh the page and try again.') );

// Show and hide success
jQuery('#pTempEmailMessage')->show()->delay(5000)->hide();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();