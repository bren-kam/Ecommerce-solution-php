<?php
/**
 * @page List - Add Custom Products
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$p = new Products;
$dt = new Data_Table;

// Set variables
$dt->order_by( 'a.`name`', 'b.`name`', 'a.`sku`', 'a.`status`' );
$dt->add_where( ' AND ( a.`website_id` = 0 || a.`website_id` = ' . (int) $user['website']['website_id'] . ')' );
$dt->add_where( " AND a.`publish_visibility` = 'public' AND a.`publish_date` <> '0000-00-00 00:00:00'" );

// Initialize variable
$data = array();

switch ( $_GET['sType'] ) {
	case 'sku':
		if ( _('Enter SKU...') == $_GET['s'] ) {
			$skip = true;
		} else {
			$dt->add_where( " AND a.`sku` LIKE '" . $dt->db->escape( $_GET['s'] ) . "%'" );
		}
	break;
	
	case 'product':
		if ( _('Enter Product Name...') == $_GET['s'] ) {
			$skip = true;
		} else {
			$dt->add_where( " AND a.`name` LIKE '" . $dt->db->escape( $_GET['s'] ) . "%'" );
		}
	break;
	
	case 'brand':
		if ( _('Enter Brand...') == $_GET['s'] ) {
			$skip = true;
		} else {
			$dt->add_where( " AND b.`name` LIKE '" . $dt->db->escape( $_GET['s'] ) . "%'" );
		}
	break;
	
	default:
		$skip = true;
	break;
}


// Do a category search
if ( !empty( $_GET['c'] ) ) {
	$skip = false;
	
	$c = new Categories;
	$category_ids = $c->get_sub_category_ids( (int) $_GET['c'] );
	$category_ids[] = (int) $_GET['c'];
	
	// Behold awesomeness (I know there may be a cleaner way -- definitely open to it)
	$dt->add_where( ' AND c.`category_id` IN(' . preg_replace( '/[^0-9,]/', '', implode( ',', $category_ids ) ) . ')' );
}

// If it was invalid, don't display anything
if ( $skip ) {
	// Send response
	echo $dt->get_response( $data );
	exit;
}


// Get products
$products = $p->list_add_products( $dt->get_variables() );
$dt->set_row_count( $p->count_add_products( $dt->get_where() ) );

// Nonce
$get_product_nonce = nonce::create( 'get-product' );
$add_product_nonce = nonce::create( 'add-product' );

// Create output
if ( is_array( $products ) )
foreach ( $products as $product ) {
	$dialog = '<a href="/dialogs/products/get-product/?_nonce=' . $get_product_nonce . '&amp;pid=' . $product['product_id'] . '#dProductDialog' . $product['product_id'] . '" title="' . _('View Product') . '" rel="dialog">';
	$actions = '<a href="javascript:;" class="add-product" id="aAddProduct' . $product['product_id'] . '" name="' . $product['name'] . '" title="' . _('Add Product') . '">' . _('Add Product') . '</a>';
	
	$data[] = array( 
		$dialog . format::limit_chars( $product['name'],  37, '...' ) . '</a><br /><div class="actions">' . $actions . '</div>',
		$product['brand'],
		$product['sku'],
		ucwords( $product['status'] )
	);
}

// Send response
echo $dt->get_response( $data );