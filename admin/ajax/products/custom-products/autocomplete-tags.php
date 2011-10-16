<?php
/**
 * @page Custom Products - Autocomplete Tags
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'autocomplete-tags' );
$ajax->ok( $user, _('You must be signed in to autocomplete tags.') );

// Instantiate class
$t = new Tags;

// Get the suggestions
$suggestions = $t->autocomplete( $_POST['term'] );

if( !$suggestions )
	$suggestions = array();

// Sent by the autocompleter
$ajax->add_response( 'suggestions', $suggestions );

// Send the response
$ajax->respond();