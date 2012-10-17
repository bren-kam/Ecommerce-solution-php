<?php
/**
 * @page Get Templates
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'get-templates' );
$ajax->ok( $user, _('You must be signed in to get email templates') );

// Instantiate class
$e = new Email_Marketing();

// Add the response
$ajax->add_response( 'templates', $e->get_templates( $_POST['type'] ) );

// Send response
$ajax->respond();