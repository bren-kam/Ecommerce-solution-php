<?php
/**
 * @page Get Product Dialog Info
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_ajax_update_product'], 'update-product' );
$ajax->ok( $user, _('You must be signed in to update a product.') );

// Instantiate class
$p = new Products;

if ( $user['website']['shopping_cart'] ) {
	$new_product_values = array( 
		'alternate_price' => $_POST['tAlternatePrice'],
		'price' => $_POST['tPrice'],
		'sale_price' => $_POST['tSalePrice'],
		'wholesale_price' => $_POST['tWholesalePrice'],
		'inventory' => $_POST['tInventory'],
		'additional_shipping_amount' => ( 'Flat Rate' == $_POST['rShippingMethod'] ) ? $_POST['tShippingFlatRate'] : $_POST['tShippingPercentage'],
		'weight' => $_POST['tWeight'],
		'protection_amount' => ( 'Flat Rate' == $_POST['rProtectionMethod'] ) ? $_POST['tProtectionFlatRate'] : $_POST['tProtectionPercentage'],
		'meta_title' => strip_tags( $_POST['tMetaTitle'] ),
		'meta_description' => strip_tags( $_POST['tMetaDescription'] ),
		'meta_keywords' => strip_tags( $_POST['tMetaKeywords'] ),
		'additional_shipping_type' => $_POST['rShippingMethod'],
		'alternate_price_name' => $_POST['tAlternatePriceName'],
		'protection_type' => $_POST['rProtectionMethod'],
		'price_note' => $_POST['tPriceNote'],
		'product_note' => $_POST['taProductNote'],
		'ships_in' => $_POST['tShipsIn'],
		'store_sku' => $_POST['tStoreSKU'],
		'warranty_length' => $_POST['tWarrantyLength'],
		'display_inventory' => ( isset( $_POST['cbDisplayInventory'] ) ) ? 1 : 0,
		'on_sale' => ( isset( $_POST['cbOnSale'] ) ) ? 1 : 0,
		'status' => $_POST['sStatus']
	);
	
	$coupons = ( empty( $_POST['hCoupons'] ) ) ? false : explode( '|', $_POST['hCoupons'] );
	
	// Set the product options
	$product_options = false;
	if ( isset( $_POST['product_options'] ) )
	switch ( $_POST['product_options'] as $po_id => $value ) {
		if ( isset( $_POST['tPrice' . $po_id] ) ) {
			$product_options[$po_id] = $_POST['tPrice' . $po_id];
		} else {
			$product_options[$po_id]['required'] = ( isset( $_POST['cbRequired' . $po_id] ) ) ? 1 : 0;
		}
	
		if ( isset( $_POST['product_list_items'][$po_id] ) )
		switch ( $_POST['product_list_items'][$po_id] as $li_id => $value ) {
			$product_options[$po_id]['list_items'][intval($li_id)] = $_POST['tPrices'][$po_id][$li_id];
		}
	}
} else {
	$new_product_values = array( 
		'alternate_price' => $_POST['tAlternatePrice'],
		'price' => $_POST['tPrice'],
		'sale_price' => $_POST['tSalePrice'],
		'inventory' => $_POST['tInventory'],
		'alternate_price_name' => $_POST['tAlternatePriceName'],
		'price_note' => $_POST['tPriceNote'],
		'product_note' => $_POST['taProductNote'],
		'warranty_length' => $_POST['tWarrantyLength'],
		'display_inventory' => ( isset( $_POST['cbDisplayInventory'] ) ) ? 1 : 0,
		'on_sale' => ( isset( $_POST['cbOnSale'] ) ) ? 1 : 0,
		'status' => $_POST['sStatus']
	);
	
	$coupons = $product_options = false; 
}

// Update the product
$ajax->ok( $p->update_product( $_POST['hProductID'], $new_product_values, $coupons, $product_options ), _('An error occurred while trying to update your product. Please refresh the page and try again.') );

jQuery('#fEditProduct .close:first')->click();
jQuery( '#sPrice' . $_POST['hProductID'] )->text( $_POST['tPrice'] );
jQuery( '#sAlternatePrice' . $_POST['hProductID'] )->text( $_POST['tAlternatePrice'] );
jQuery( '#sAlternatePriceName' . $_POST['hProductID'] )->text( $_POST['tAlternatePriceName'] );

// Add response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();