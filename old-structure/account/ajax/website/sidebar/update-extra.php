<?php
/**
 * @page Update image
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_ajax_update_extra'], 'update-extra' );
$ajax->ok( $user, _('You must be signed in to update this field.') );

// Instantiate class
$wa = new Website_Attachments();

// Empty it the link they didn't enter anything
if ( 'Enter Link...' == $_POST['extra'] )
    $_POST['extra'] = '';

$meta = ( isset( $_POST['meta'] ) ) ? $_POST['meta'] : '';

// Do validation
$v = new Validator();
$v->add_validation( 'extra', 'URL' );

$ajax->ok( empty( $errs ) && stristr( $_POST['extra'], 'http' ), _('Please make sure you enter in a valid link') );

// Make sure it updated successfully
$ajax->ok( $wa->update_extra( $_POST['hWebsiteAttachmentID'], $_POST['extra'], $meta ), _('An error occurred while trying to update your field. Please refresh the page and try again.') );

// Show and hide success
jQuery( '#' . $_POST['target'] )->show()->delay(5000)->hide();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();