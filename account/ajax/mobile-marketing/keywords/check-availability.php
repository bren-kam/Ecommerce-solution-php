<?php
/**
 * @page Check Keyword Availability
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'check-availability' );
$ajax->ok( $user, _('You must be signed in to delete a keyword.') );

// Instantiate class
$m = new Mobile_Marketing();

if ( $m->check_keyword_availability( $_POST['k'] ) ) {
    jQuery('#sAvailable')->removeClass('error')->addClass('success')->text('Keyword is available!');
    jQuery('#hKeywordAvailable')->val(1);
} else {
    jQuery('#sAvailable')->removeClass('success')->addClass('error')->text('Keyword is unavailable!');
    jQuery('#hKeywordAvailable')->val(0);
}

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();