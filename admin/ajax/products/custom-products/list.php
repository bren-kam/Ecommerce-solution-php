<?php
/**
 * @page List Custom Products
 * @package Imagine Retailer
 * @subpackage Account
 */

// Instantiate classes
$p = new Products;
$dt = new Data_Table;

// Set variables
$dt->order_by( 'a.`name`', 'd.`name`', 'a.`sku`', 'c.`name`', 'a.`status`', 'a.`publish_date`' );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] );
$dt->search( array( 'a.`name`' => true, 'd.`name`' => false, 'a.`sku`' => false ) );

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
			$dt->add_where( " AND d.`name` LIKE '" . $dt->db->escape( $_GET['s'] ) . "%'" );
		}
	break;
	
	default:
		$skip = true;
	break;
}

// Get products
$products = $p->list_custom_products( $dt->get_variables() );
$dt->set_row_count( $p->count_custom_products( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this product? This cannot be undone.');
$delete_custom_product_nonce = nonce::create( 'delete-custom-product' );

// Create output
if ( is_array( $products ) )
foreach ( $products as $product ) {
	$actions = '<a href="/products/custom-products/add-edit/?pid=' . $product['product_id'] . '" title="' . _('Edit Product') . '">' . _('Edit') . '</a>';
	$actions .= ' | <a href="/products/custom-products/clone/?pid=' . $product['product_id'] . '" title="' ._('Clone Product') . '">Clone</a>';
	$actions .= ' | <a href="/ajax/products/custom-products/delete/?_nonce=' . $delete_custom_product_nonce . '&amp;pid= ' . $product['product_id'] . '" title="' . _('Delete Product') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
		
	$data[] = array( 
		$product['name'] . '<div class="actions">' . $actions . '</div>',
		$product['brand'],
		$product['sku'],
		$product['category'],
		ucwords( $product['status'] ),
		$product['publish_date']
	);
}

// Send response
echo $dt->get_response( $data );