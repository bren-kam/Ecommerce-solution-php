<?php
/**
 * @page Set Values
 * @package Imagine Retailer
 */

 // Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'set-values' );
$ajax->ok( $user, _('You must be signed in to set product prices.') );

$p = new Products();

// Set product prices
$ajax->ok( $p->set_product_prices( $_POST['v'] ), _('An error occurred while trying to set your product prices. Please refresh the page and try again.') );

jQuery('span.success')->show()->delay(5000)->hide();

$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();