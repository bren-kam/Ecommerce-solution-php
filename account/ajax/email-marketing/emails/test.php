<?php
/**
 * @page Test Message
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'test-message' );
$ajax->ok( $user, _('You must be signed in to send a test message') );

// Instantiate class
$e = new Email_Marketing();

// Add the response
$e->test_message( $_POST['email'], $_POST['emid'] );

// Send response
$ajax->respond();