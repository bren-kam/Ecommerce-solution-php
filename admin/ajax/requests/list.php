<?php
/**
 * @page List Requests
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

$order_by = '';
		
/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch ( $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = 'days_left';
			break;

			case 1:
				$field = 'b.`title`';
			break;

			case 2:
				$field = 'c.`contact_name`';
			break;

			case 3:
				$field = 'a.`type`';
			break;
			
			case 4:
				$field = 'a.`date_created`';
			break;
			
			case 5:
				$field = 'b.`live`';
			break;
		}
			
		$order_by .= $field . ', a.`request_id` ' . $_GET['sSortDir_' . $i] . ", ";
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}

$r = new Requests;

/* Filtering  */
$where = ' AND a.`status` = ' . (int) $_GET['status'];

if ( !empty( $_GET['sSearch'] ) ) {
	$search = $r->db->escape( $_GET['sSearch'] );
	$where .= " AND ( b.`title` LIKE '{$search}%' OR c.`contact_name` LIKE '{$search}' )";
}

$requests = $r->list_requests( $where, $order_by, $limit );
$request_count = $r->count_requests( $where );

$aaData = array();

//foreach ( $visitors as $v ) {
foreach ( $requests as $r ) {
	$name = ( 'Anonymous' == $r['user_name'] ) ? _('Anonymous ') . $r['request_id'] : $r['user_name'];
	
	if ( '0' == $_GET['status'] ) {
		// Determined which color should be used for days left
		switch ( $r['days_left'] ) {
			case ( $r['days_left'] < 10 ):
				$color = 'red';
			break;
				
			case ( $r['days_left'] < 20 ):
				$color = 'orange';
			break;
			
			default:
				$color = 'green';
			break;
		}
		
		$first = '<span class="' . $color . '">' . $r['days_left'] . '</span>';
	} else {
		$first = $r['date_updated'];
	}
	
	$aaData[] = array( $first, $r['title'] . '<br /><span id="sViewActions' . $r['request_id'] . '"><a title="' . _('View Request') . '" href="/requests/view/?rid=' . $r['request_id'] . '">' . _('View') . '</a>&nbsp;|&nbsp;<a href="javascript:;" id="aDelete' . $r['request_id'] . '" class="delete-request" title="' . _('Delete Request') . '">' . _('Delete') . '</a></span>', addslashes( $r['contact_name'] ), $r['type'], $r['date_created'], ( '0' == $r['live'] ) ? _('Staging') : _('Live') );
}

echo json_encode( array(
	'sEcho' => ( isset( $_GET['sEcho'] ) ) ? intval( $_GET['sEcho'] ) : 1,
	'iTotalRecords' => $request_count,
	'iTotalDisplayRecords' => $request_count,
	'aaData' => $aaData
) );