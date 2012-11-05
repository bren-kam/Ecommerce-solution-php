<?php
/**
 * @page Email Marketing - Offer Box
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'offer-box' );
$ajax->ok( $user, _('You must be signed in to get a product for the offer box.') );

// Instantiate Class
$p = new Products;

// Get the product
$product = $p->get_website_product( $_POST['pid'] );

// Form the response HTML
$product_box = '<div id="dProduct_' . $product['product_id'] . '" class="product offer-box">';
$product_box .= '<h4>' . format::limit_chars( $product['name'], 37 ) . '</h4>';
$product_box .= '<p align="center"><img src="http://' . $product['industry'] . '.retailcatalog.us/products/' . $product['product_id'] . '/' . $product['image'] . '" alt="' . $product['name'] . '" height="110" style="margin:10px" /></p>';
$product_box .= '<p>' . _('Brand') . ': ' . $product['brand'] . '<br /><label for="tProductPrice' . $product['productID'] . '">' . _('Price') . ':</label> <input type="text" name="tProductPrice' . $product['product_id'] . '" class="tb product-box-price" id="tProductPrice' . $product['productID'] . '" value="' . $product['price'] . '" maxlength="10" /></p>';
$product_box .= '<p class="product-actions" id="pProductAction' . $product['productID'] . '"><a href="javascript:;" class="remove-box-product" title="' . _('Remove Product') . '">' . _('Remove') . '</a></p>';
$product_box .= '<input type="hidden" name="hProduct' . $_POST['bid'] . '" id="hProduct' . $_POST['bid'] . '" value="' . $product['product_id'] . '|' . $product['price'] . '" />';
$product_box .= '</div>';
	
jQuery( '#dProductContainer' . $_POST['bid'])->html( $product_box );
jQuery( '#dProduct' . $_POST['bid'] )->show();

// Add the jQuery
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send the response
$ajax->respond();