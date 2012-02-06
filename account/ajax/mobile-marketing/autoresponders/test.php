<?php
/**
 * @page Test Autoresponder
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'test-autoresponder' );
$ajax->ok( $user, _('You must be signed in to test an autoresponder.') );

// Instantiate class
$e = new Email_Marketing();

// Delete user
$ajax->ok( $e->test_autoresponder( $_POST['e'], $_POST['s'], $_POST['m'], $_POST['co'] ), _('An error occurred while trying to test your autoresponder. Please refresh the page and try again.') );

// Show the success message
jQuery('#pSuccessMessage')->show()->delay(5000)->hide();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();