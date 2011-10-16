<?php
/**
 * @page Add Shipping Method
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'add-shipping-method' );
$ajax->ok( $user, _('You must be signed in to add shipping methods.') );

global $user;

$s = new Shopping_Cart;

$ajax->ok( $id = $s->add_shipping_method( $_POST['tName'], $_POST['sMethod'], $_POST['tAmount'], $_POST['hID'] ), _('An error occurred while trying to update your shipping method. Please refresh the page and try again.') );

$percentage = ( $_POST['sMethod'] == 'Percentage' ) ? true : false;

$html = '<tr id="dShippingMethod' . $id . '">'
.'<td title="Shipping Name"><a rel="dialog" cache="0" href="/dialogs/edit-zip-codes/?wsmid=' . $sm['website_shipping_method_id'] . '#dEditZipCodes" class="zip-codes" ajax="true" title="Edit Zip Codes"><span>' . $_POST['tName'] . '</span></a></td>'
.'<td title="Shipping Method"><span>' . $_POST['sMethod'] . '</span></td>'
.'<td title="Shipping Amount"><span>' . ( ( !$percentage ) ? '$' : '' ) . number_format( $_POST['tAmount'], 2 ) . ( ( $percentage ) ? '%' : '' ) . '</span></td>'
.'<td title="Actions"><span><a rel="dialog" cache="0" href="/dialogs/edit-shipping/?wsmid=' . $id . '#dEditShipping" class="edit-shipping-method" title="Edit Shipping Method">Edit</a> | <a href="/ajax/shopping-cart/delete-shipping-method/?wsmid=' . $id . '&_nonce=' . nonce::create( 'delete-shipping-method' ) . '" ajax="1" id="aDelete' . $id . ' class="delete-shipping-method" title="Delete Shipping Method">Delete</a></span></td>'
.'</tr>';

jQuery( '#trAddShippingMethod' )->before( $html );
jQuery( '#dShippingMethod' . $_POST['hID'] )->sparrow();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();