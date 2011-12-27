<?php
/**
 * @page List Products
 * @package Imagine Retailer
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user ) {
	echo json_encode( array( 
		'redirect' => true,
		'sEcho' => intval( $_GET['sEcho'] ),
		'iTotalRecords' => 0,
		'iTotalDisplayRecords' => 0,
		'aaData' => array()
	) );
	return false;
}

/* Paging */
$limit = ( isset( $_GET['iDisplayStart'] ) ) ? intval( $_GET['iDisplayStart'] ) . ', ' . intval( $_GET['iDisplayLength'] ) : 1000;

if ( '0' == $_GET['iDisplayLength'] )
    $limit = 0;

$order_by = '';

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch ( $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = 'a.`name`';
			break;

			case 1:
				$field = 'd.`name`';
			break;

			case 2:
				$field = 'a.`sku`';
			break;

			case 3:
				$field = 'c.`name`';
			break;
			
			case 4:
				$field = 'a.`status`';
			break;
			
			case 5:
				$field = 'a.`publish_date`';
			break;
		}
			
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}

// Instantiate classes
$p = new Products;

/* Filtering  */
$where = ( isset( $_SESSION['products']['visibility'] ) ) ? " AND `publish_visibility` = '" . $p->db->escape( $_SESSION['products']['visibility'] ) . "'" : " AND `publish_visibility` <> 'deleted'";

if ( isset( $_SESSION['products']['product-status'] ) && isset( $_SESSION['products']['user'] ) ) {
	switch ( $_SESSION['products']['product-status'] ) {
		case 'created':
			$where .= ' AND `user_id_created` = ' . (int) $_SESSION['products']['user'];
		break;
		
		case 'modified':
			$where .= ' AND `user_id_modified` = ' . (int) $_SESSION['products']['user'];
		break;
	}
}

if ( isset( $_SESSION['products']['search'] ) ) {
	if ( isset( $_SESSION['products']['type'] ) ) {
		switch ( $_SESSION['products']['type'] ) {
			case 'products':
				$type = 'a.`name`';
			break;
			
			default:
			case 'sku':
				$type = 'a.`sku`';
			break;
			
			case 'brands':
				$type = 'd.`name`';
			break;
		}
	} else {
		$type = 'a.`sku`';
	}
	
	$where .= " AND ( $type LIKE '" . $p->db->escape( $_SESSION['products']['search'] ) . "%' )";
}

// Add categories
if ( 0 != $_GET['cid'] ) {
    $c = new Categories();
    $categories = $c->get_sub_category_ids( $_GET['cid'] );
    $categories[] = $_GET['cid'];

    // Make sure they are all integers
    foreach ( $categories as &$cat ) {
        $cat = (int) $cat;
    }
    
    $where .= ' AND b.`category_id` IN(' . implode( ',', $categories ) . ')';
}

// Get websites
$products = $p->list_products( $where, $order_by, $limit );
$product_count = $p->count_products( $where );

$aaData = array();

if ( is_array( $products ) )
foreach ( $products as $product ) {
	$aaData[] = array( '<span>' . $product['name']  . '</span><br />
		<div>
			<a href="/products/add-edit/?pid=' . $product['product_id'] . '" title=\'' . _('Edit') . ' "' . $product['name'] . '"\' class="edit-product">' . _('Edit') . '</a> |
			<a href="javascript:;" id="aDelete' . $product['product_id'] . '" title=\'' . _('Delete') . ' "' . $product['name'] . '"\' class="delete-product">' . _('Delete') . '</a> | 
			<a href="/products/clone/?pid=' . $product['product_id'] . '" title=\'' . _('Clone') . ' "' . $product['name'] . '"\' target="_blank">' . _('Clone') . '</a>
		</div>', $product['brand'], $product['sku'], ucwords( $product['status'] ), $product['publish_date'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $product_count,
	'iTotalDisplayRecords' => $product_count,
	'aaData' => $aaData
) );