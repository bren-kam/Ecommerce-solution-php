<?php
/**
 * @page List Users
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
				$field = 'a.`contact_name`';
			break;

			case 1:
				$field = 'a.`email`';
			break;

			case 2:
				$field = 'phone';
			break;
			
			case 3:
				$field = 'b.`domain`';
			break;

			case 4:
				$field = 'a.`role`';
			break;
			
			//case 4:
			//	$field = 'a.`date_created';
			//break;
		}
			
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}


/* Filtering  */
$where = "";
if ( $_GET['sSearch'] != "" ) {
	$search = $u->db->escape( $_GET['sSearch'] );
	
	$where = " AND ( a.`contact_name` LIKE '%$search%' OR a.`email` LIKE '%$search%' OR b.`domain` LIKE '%$search%' )";
}

$users = $u->list_users( $where, $order_by, $limit );
$user_count = $u->count_users( $where );

$aaData = array();

if ( is_array( $users ) )
foreach ( $users as $us ) {
	switch ( $us['role'] ) {
		case 1: 
			$role = 'Basic User';
		break;
		
		case 5:
			$role = 'Basic Account';
		break;
		
		case 7:
			$role = 'Online Specialist';
		break;
		
		case 8:
			$role = 'Admin';
		break;
		
		case 10:
			$role = 'Super Admin';
		break;
		
		default:
			$role = 'Unknown - ' . $us['role'];
		break;
	}
	
	// $aaData[] = array( '<a href="/users/edit/?uid=' . $us['user_id'] . '" title="' . $us['contact_name'] . '">' . $us['contact_name'] . '</a>', '<a href="mailto:' . $us['email'] . '" title="' . _('Email') . ' ' . $us['contact_name'] . '">' . $us['email'] . '</a>', $us['domain'], $role, dt::date( 'm-d-Y', $us['date_created'] ) );
	$aaData[] = array( 
		'<a href="/users/edit/?uid=' . $us['user_id'] . '" title="' . $us['contact_name'] . '">' . $us['contact_name'] . ' (' . _('Edit') . ')' . '</a>', 
		'<a href="mailto:' . $us['email'] . '" title="' . _('Email') . ' ' . $us['contact_name'] . '">' . $us['email'] . '</a>', 
		$us['phone'], 
		'<a href="http://' . $us['domain'] . '/" target="_blank">' . $us['domain'] . "</a>", 
		$role 
	);
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $user_count,
	'iTotalDisplayRecords' => $user_count,
	'aaData' => $aaData
) );