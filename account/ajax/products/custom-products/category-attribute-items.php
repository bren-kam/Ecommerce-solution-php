<?php
/**
 * @page Category Attribute Items
 * @package Imagine Retailer
 */

 // Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'category-attribute-items' );
$ajax->ok( $user, _('You must be signed in to get category attributes.') );

$a = new Attributes;

// Get the attributes
$attributes = ( empty( $_POST['c'] ) ) ? $a->get_attribute_items() : $a->get_attribute_items_by_categories( $_POST['c'] );

// Sort through the list
$attribute_list = array_keys( $attributes );

$options = '';
$disabled_attributes = $_POST['da'];

// Loop through the attributes list
foreach ( $attribute_list as $al ) {
	$options .= '<optgroup label="' . $al . '">';
	
	switch ( $attributes[$al] as $attribute ) {
		$disabled = ( in_array( $attribute['attribute_item_id'], $disabled_attributes ) ) ? ' disabled="disabled"' : '';
		
		$options .= '<option value="' . $attribute['attribute_item_id'] . '"' . $disabled . '>' . $attribute['attribute_item_name'] . '</option>';
	}
	
	$options .= '</optgroup>';
}

// Update the select
jQuery('#sAttributes')->html( $options );

// Add response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// AJAX response
$ajax->respond();