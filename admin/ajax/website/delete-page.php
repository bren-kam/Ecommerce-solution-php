<?php
/**
 * @page Delete Page
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'delete-page' );
$ajax->ok( $user, _('You must be signed in to delete a page.') );
$ajax->ok( $user['role'] >= 7, _('You do not have permission to delete this website.') );

// Instantiate class
$w = new Websites();

// Delete user
$ajax->ok( $w->delete( $_GET['wpid'] ), _('An error occurred while trying to delete your website. Please refresh the page and try again.') );

// Redraw the table
jQuery('.dt:first')->dataTable()->fnDraw();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();