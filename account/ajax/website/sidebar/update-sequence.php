<?php
/**
 * @page Update sequence
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'update-sequence' );
$ajax->ok( $user, _('You must be signed in to update your sidebar sequence.') );

// Instantiate class
$wa = new Website_Attachments();

// Determine the sequence and behold the awesomeness that is the next two lines
$sequence = explode( '&dAttachment[]=', $_POST['s'] );
$sequence[0] = substr( $sequence[0], 14 );

// Make sure it updated successfully
$ajax->ok( $wa->update_sequence( $sequence ), _('An error occurred while trying to update the sequence of your sidebar elements. Please refresh the page and try again.') );

// Send response
$ajax->respond();