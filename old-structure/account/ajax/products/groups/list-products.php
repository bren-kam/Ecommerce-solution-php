<?php
/**
 * @page List Products for Send Email
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$p = new Products;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'a.`name`', 'b.`name`', 'a.`sku`', 'a.`status`', 'a.`name`' );
$dt->add_where( ' AND ( a.`website_id` = 0 || a.`website_id` = ' . (int) $user['website']['website_id'] . ' )' );
$dt->add_where( ' AND c.`website_id` = ' . (int) $user['website']['website_id'] );
$dt->add_where( " AND a.`publish_visibility` = 'public' AND a.`publish_date` <> '0000-00-00 00:00:00'" );

// Initialize variables
$skip = false;
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

// If it was invalid, don't display anything
if ( $skip ) {
	// Send response
	echo $dt->get_response( $data );
	exit;
}

// Get products
$products = $p->list_website_products( $dt->get_variables() );
$dt->set_row_count( $p->count_website_products( $dt->get_where() ) );

// Nonce
$get_website_product_nonce = nonce::create( 'get-website-product' );
$add_website_product_nonce = nonce::create( 'add-website-product' );

// Create output
if ( is_array( $products ) )
foreach ( $products as $product ) {
	$dialog = '<a href="/dialogs/products/groups/get-website-product/?_nonce=' . $get_website_product_nonce . '&amp;pid=' . $product['product_id'] . '#dProductDialog' . $product['product_id'] . '" title="' . _('View Product') . '" rel="dialog">';
	$actions = '<a href="/ajax/products/groups/add-website-product/?_nonce=' . $add_website_product_nonce . '&amp;pid=' . $product['product_id'] . '" title="' . _('Add Product') . '" ajax="1">' . _('Add Product') . '</a>';
	$data[] = array( 
		$dialog . format::limit_chars( $product['name'],  50, '...' ) . '</a><br /><div class="actions">' . $actions . '</div>',
		$product['brand'],
		$product['sku'],
		$product['status']
	);
}

// Send response
echo $dt->get_response( $data );