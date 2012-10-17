<?php
/**
 * @page List Attributes
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
				$field = '`title`';
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
	
	$where = " AND ( `title` LIKE '%$search%' )";
}

$a = new Attributes;
$attributes = $a->list_attributes( $where, $order_by, $limit );
$attribute_count = $a->count_attributes( $where );

$aaData = array();

if ( is_array( $attributes ) )
foreach ( $attributes as $at ) {
	$aaData[] = array( '<span>' . $at['title'] . '</span><br /><div><a href="/attributes/edit/?aid=' . $at['attribute_id'] . '" title="' . _('Edit Attribute') . '">' . _('Edit') . '</a> | <a href="javascript:;" id="aDeleteAttribute' . $at['attribute_id'] . '" title="' . _('Delete Attribute') . '" class="delete-attribute">' . _('Delete') . '</a></div>' );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $attribute_count,
	'iTotalDisplayRecords' => $attribute_count,
	'aaData' => $aaData
) );