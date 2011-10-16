<?php
/**
 * @page List Product Options
 * @package Real Statistics
 * @subpackage Admin
 */
 
// Get current user
global $user;

// If user is not logged in
if( !$user ) {
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

$order_by = '';

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch(  $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = '`option_title`';
			break;
			
			case 1:
				$field = '`option_name`';
			break;
			
			case 2:
				$field = '`option_type`';
			break;
		}
			
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}


/* Filtering  */
$where = "";
if ( $_GET['sSearch'] != "" ) {
	$search = $u->db->escape( $_GET['sSearch'] );
	
	$where = " AND ( `option_title` LIKE '%$search%' OR `option_name` LIKE '%$search%' OR `option_type` LIKE '%$search%' )";
}

$po = new Product_Options;
$product_options = $po->list_product_options( $where, $order_by, $limit );
$product_option_count = $po->count_product_options( $where );

$aaData = array();

if( is_array( $product_options ) )
foreach( $product_options as $po ) {
	$aaData[] = array( '<span>' . $po['option_title'] . '</span><br /><div><a href="/product-options/add-edit/?poid=' . $po['product_option_id'] . '" title="' . _('Edit Product Option') . '">' . _('Edit') . '</a> | <a href="javascript:;" id="aDeleteProductOption' . $po['product_option_id'] . '" title="' . _('Delete Product Option') . '" class="delete-product-option">' . _('Delete') . '</a></div>', $po['option_name'], $po['option_type'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $product_option_count,
	'iTotalDisplayRecords' => $product_option_count,
	'aaData' => $aaData
) );