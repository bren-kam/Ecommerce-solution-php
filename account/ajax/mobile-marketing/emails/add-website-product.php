<?php
/**
 * @page Add Website Product
 * @package Imagine Retailer
 */
 
 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'add-website-product' );
$ajax->ok( $user, _('You must be signed in to add a website product') );

// Instantiate class
$p = new Products();

$product = $p->get_website_product( $_GET['pid'] );

// Form the response HTML
$product_box = '<div id="dProduct_' . $product['product_id'] . '" class="product">';
$product_box .= '<h4>' . format::limit_chars( $product['name'], 37 ) . '</h4>';
$product_box .= '<p align="center"><img src="http://' . $product['industry'] . '.retailcatalog.us/products/' . $product['product_id'] . '/' . $product['image'] . '" alt="' . $product['name'] . '" height="110" style="margin:10px" /></p>';
$product_box .= '<p>' . _('Brand') . ': ' . $product['brand'] . '<br /><label for="tProductPrice' . $product['productID'] . '">' . _('Price') . ':</label> <input type="text" name="tProductPrice' . $product['product_id'] . '" class="tb product-box-price" id="tProductPrice' . $product['productID'] . '" value="' . $product['price'] . '" maxlength="10" /></p>';
$product_box .= '<p class="product-actions" id="pProductAction' . $product['productID'] . '"><a href="javascript:;" class="remove-product" title="' . _('Remove Product') . '">' . _('Remove') . '</a></p>';
$product_box .= '<input type="hidden" name="products[]" class="hidden-product" id="hProduct' . $product['product_id'] . '" value="' . $product['product_id'] . '|' . $product['price'] . '" />';
$product_box .= '</div>';
	
jQuery('#dSelectedProducts')->append( $product_box )->changeCount(1);

$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();
?>