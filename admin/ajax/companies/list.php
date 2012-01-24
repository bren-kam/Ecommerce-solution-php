<?php
/**
 * @page List Companies
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


$c = new Companies();

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
				$field = '`domain`';
			break;

			case 2:
				$field = '`date_created`';
			break;
		}
			
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}


/* Filtering  */
$where = '';
if ( $_GET['sSearch'] != "" ) {
	$where .= " AND ( `name` LIKE '%" . $t->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"`domain` LIKE '%" . $t->db->escape( $_GET['sSearch'] ) . "%' )";
}

$companies = $c->list_companies( $limit, $where, $order_by );
$company_count = $c->count_companies( $where );

$aaData = array();

if ( is_array( $companies ) )
foreach ( $companies as $company ) {
	$aaData[] = array( '<a href="/companies/add-edit/?cid=' . $company['company_id'] . '" title="Edit Company">' . $company['name'] . '</a>', $company['domain'], dt::date( 'm/d/Y', $company['date_created'] ) );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $company_count,
	'iTotalDisplayRecords' => $company_count,
	'aaData' => $aaData
) );