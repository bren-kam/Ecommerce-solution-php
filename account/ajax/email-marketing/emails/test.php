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
$response = $e->test_message( $_POST['email'], $_POST['emid'] );

$ajax->ok( $response->success(), _('An error occurred while trying to send out your test email: ') . $response->message() );

// Send response
$ajax->respond();