<?php
/**
 * @page List Brands
 * @package Real Statistics
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

$order_by = '';

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch (  $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = '`name`';
			break;

			case 1:
				$field = '`link`';
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
	
	$where = " AND ( `name` LIKE '%$search%' OR `link` LIKE '%$search%' )";
}

$b = new Brands;

$brands = $b->list_brands( $where, $order_by, $limit );
$brand_count = $b->count_brands( $where );

$aaData = array();

if ( is_array( $brands ) )
foreach ( $brands as $br ) {
	$aaData[] = array( '<span>' . $br['name'] . '</span><br /><div><a href="/brands/edit/?bid=' . $br['brand_id'] . '" title="' . _('Edit Brand') . '">' . _('Edit') . '</a> | <a href="javascript:;" id="aDeleteBrand' . $br['brand_id'] . '" title="' . _('Delete Brand') . '" class="delete-brand">' . _('Delete') . '</a></div>', $br['link'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $brand_count,
	'iTotalDisplayRecords' => $brand_count,
	'aaData' => $aaData
) );