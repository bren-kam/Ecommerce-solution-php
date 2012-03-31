<?php
/**
 * @page Update sequence
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'update-sequence' );
$ajax->ok( $user, _('You must be signed in to update your brand sequence.') );

// Instantiate class
$b = new Brands;

// Determine the sequence and behold the awesomeness that is the next two lines
$sequence = explode( '&dBrand[]=', $_POST['s'] );
$sequence[0] = substr( $sequence[0], 9 );

// Make sure it updated successfully
$ajax->ok( $b->update_sequence( $sequence ), _('An error occurred while trying to update the sequence of your brands. Please refresh the page and try again.') );

// Send response
$ajax->respond();