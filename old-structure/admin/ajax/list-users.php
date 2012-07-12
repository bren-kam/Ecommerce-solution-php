<?php
/**
 * @page List Users
 * @package Real Statistics
 * @subpackage Admin
 */
 

/* Paging */
$limit = ( isset( $_GET['iDisplayStart'] ) ) ? intval( $_GET['iDisplayStart'] ) . ', ' . intval( $_GET['iDisplayLength'] ) : 1000;

$order_by = '';

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch (  $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = 'name';
			break;

			case 1:
				$field = 'a.`email`';
			break;

			case 2:
				$field = 'b.`name`';
			break;

			case 3:
				$field = 'a.`users_limit`';
			break;
			
			case 4:
				$field = 'a.`status`';
			break;

			case 5:
				$field = 'a.`date_created`';
			break;
		}
			
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}


/* Filtering  */
$where = "";
if ( $_GET['sSearch'] != "" ) {
	$where = " AND ( name LIKE '%" . $u->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"a.`email` LIKE '%" . $u->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"a.`first_name` LIKE '%" . $u->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"a.`last_name` LIKE '%" . $u->db->escape( $_GET['sSearch'] ) . "%' )";
}

$users = $u->list_users( $limit, $where, $order_by );
$user_count = $u->count( $where );

$aaData = array();

if ( is_array( $users ) )
foreach ( $users as $us ) {
	switch ( $us['status'] ) {
		case -1: 
			$status = 'Not Active';
		break;
		
		case 0:
			$status = 'Deactivated';
		break;
		
		case 1:
			$status = 'Active';
		break;
		
		default:
			$status = $us['status'];
		break;
	}
	
	$monthly = ( '1' == $us['monthly'] ) ? 'Monthly' : 'Yearly';
	
	$aaData[] = array( '<a href="/user/?uid=' . $us['user_id'] . '" title="' . $us['name'] . '">' . $us['name'] . '</a>', $us['email'], $us['account_type'], $us['users_limit'], $monthly, $status, dt::date( 'm-d-Y', $us['date_created'] ) );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $user_count,
	'iTotalDisplayRecords' => $user_count,
	'aaData' => $aaData
) );