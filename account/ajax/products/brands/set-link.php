<?php
/**
 * @page Brands - Set Link
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'set-link' );
$ajax->ok( $user, _('You must be signed in to link brands to websites.') );

$w = new Websites;

$ajax->ok( $w->update( array( 'link_brands' => $_POST['checked'] ), 's' ), _('An error occurred while trying to update your website. Please refresh the page and try again.') );

// Send the response
$ajax->respond();