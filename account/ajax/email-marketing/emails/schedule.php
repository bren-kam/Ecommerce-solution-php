<?php
/**
 * @page Get Templates
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'schedule-email' );
$ajax->ok( $user, _('You must be signed in to schedule an email') );

// Instantiate class
$e = new Email_Marketing();

// Schedule email
$e->schedule_email( $_POST['emid'] );

// Send response
$ajax->respond();