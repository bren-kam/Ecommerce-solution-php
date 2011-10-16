<?php
/**
 * @page Remove Sale Items
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'remove-sale-items' );
$ajax->ok( $user, _('You must be signed in to remove sale items.') );

// Instantiate class
$p = new Products();

// Delete user
$ajax->ok( $p->remove_sale_items(), _('An error occurred while trying to remove all your sale items. Please refresh the page and try again.') );

// Redraw the table
jQuery('#sRemoveAllSaleItems')->show()->delay(5000)->hide();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();