<?php
/**
 * @page List Products for Send Email
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$p = new Products;
$dt = new Data_Table();

// If it was invalid, don't display anything
if ( empty( $_GET['b'] ) || 0 == $_GET['b'] ) {
	// Send response
	echo $dt->get_response( array() );
	exit;
}

// Set variables
$dt->order_by( 'b.`sku`', 'a.`price`', 'a.`price_note`', 'a.`alternate_price_name`', 'a.`sale_price`' );
$dt->add_where( ' AND a.`website_id` = ' . (int) $user['website']['website_id'] );
$dt->add_where( ' AND b.`brand_id` = ' . (int) $_GET['b'] );

// Get products
$products = $p->list_product_prices( $dt->get_variables() );
$dt->set_row_count( $p->count_product_prices( $dt->get_where() ) );

// Create output
if ( is_array( $products ) )
foreach ( $products as $product ) {
	$data[] = array(
		$product['sku']
		, '<input type="text" class="price" id="tPrice' . $product['product_id'] . '" value="' . $product['price'] . '" />'
        , '<input type="text" class="price_note" id="tPriceNote' . $product['product_id'] . '" value="' . $product['price_note'] . '" />'
        , '<input type="text" class="alternate_price_name" id="tAlternatePriceName' . $product['product_id'] . '" value="' . $product['alternate_price_name'] . '" />'
        , '<input type="text" class="alternate_price" id="tAlternatePrice' . $product['product_id'] . '" value="' . $product['alternate_price'] . '" />'
        , '<input type="text" class="sale_price" id="tSalePrice' . $product['product_id'] . '" value="' . $product['sale_price'] . '" />'
    );
}

// Send response
echo $dt->get_response( $data );