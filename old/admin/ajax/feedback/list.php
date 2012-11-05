<?php
/**
 * @page List Feedback
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

$f = new Feedback();

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
				$field = 'a.`message`';
			break;

			case 2:
				$field = 'a.`priority`';
			break;
			
			case 3:
				$field = 'a.`status`';
			break;
			
			case 4:
				$field = 'assigned_to';
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
	$where = " AND ( b.`first_name` LIKE '%" . $f->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"b.`last_name` LIKE '%" . $f->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"a.`message` LIKE '%" . $f->db->escape( $_GET['sSearch'] ) . "%' )";
}

// Grab only the right status
if ( isset( $_SESSION['status'] ) )
	$where .= ' AND a.`status` = ' . (int) $_SESSION['status'];

$feedback = $f->list_feedback( $limit, $where, $order_by );
$feedback_count = $f->count( $where );

$aaData = array();

foreach ( $feedback as $fb ) {
	switch ( $fb['status'] ) {
		case 0:
			$status = 'Open';
		break;
		
		case 1:
			$status = 'Closed';
		break;
		
		default:
			$status = $fb['status'];
		break;
	}
	
	switch ( $fb['priority'] ) {
		case 0:
			$priority = '<span class="green">Low</span>';
		break;
		
		case 1:
			$priority = '<span class="yellow">Medium</span>';
		break;
		
		case 2:
			$priority = '<span class="red">High</span>';
		break;
	}
	
	$aaData[] = array( $fb['name'], '<a href="/view-feedback/?fid=' . $fb['feedback_id'] . '" title="View Feedback">' . format::limit_chars( $fb['message'], 55 ) . '</a>', "$priority", $status, $fb['assigned_to'], dt::date( 'm/d/Y', $fb['date_created'] ) );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $feedback_count,
	'iTotalDisplayRecords' => $feedback_count,
	'aaData' => $aaData
) );