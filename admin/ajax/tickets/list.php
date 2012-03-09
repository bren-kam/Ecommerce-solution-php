<?php
/**
 * @page List Tickets
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


$t = new Tickets();

/* Paging */
$limit = ( isset( $_GET['iDisplayStart'] ) ) ? intval( $_GET['iDisplayStart'] ) . ', ' . intval( $_GET['iDisplayLength'] ) : 1000;

$order_by = '';

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch (  $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = 'a.`summary`';
			break;

			case 1:
				$field = 'name';
			break;

			case 2:
				$field = 'd.`title`';
			break;
			
			case 3:
				$field = 'a.`priority`';
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
$where = ( '0' == $_SESSION['tickets']['assigned-to'] ) ? ' AND ( ' . $user['role'] . ' >= COALESCE( c.`role`, 7 ) OR a.`user_id` = ' . $user['user_id'] . ' )' : ' AND ' . $user['role'] . ' >= COALESCE( c.`role`, 7 )';
if ( $_GET['sSearch'] != "" ) {
	$where .= " AND ( b.`contact_name` LIKE '%" . $t->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"`title` LIKE '%" . $t->db->escape( $_GET['sSearch'] ) . "%' OR " .
					"a.`summary` LIKE '%" . $t->db->escape( $_GET['sSearch'] ) . "%' )";
}

// Grab only the right status
if ( isset( $_SESSION['tickets']['status'] ) ) {
	$where .= ' AND a.`status` = ' . $_SESSION['tickets']['status'];
} else {
	$where .= ' AND a.`status` = 0';
}

// Grab only the right status
if ( !empty( $_SESSION['tickets']['assigned-to'] ) && '0' != $_SESSION['tickets']['assigned-to'] )
	$where .= ( '-1' == $_SESSION['tickets']['assigned-to'] ) ? ' AND c.`role` <= ' . $user['role'] : ' AND c.`user_id` = ' . $_SESSION['tickets']['assigned-to'];

$tickets = $t->list_tickets( $limit, $where, $order_by );
$ticket_count = $t->count( $where );

$aaData = array();

if ( is_array( $tickets ) )
foreach ( $tickets as $ticket ) {
	switch ( $ticket['priority'] ) {
		case 0:
			$priority = '<span class="normal">NORMAL</span>';
		break;
		
		case 1:
			$priority = '<span class="high">HIGH</span>';
		break;
		
		case 2:
			$priority = '<span class="urgent">URGENT</span>';
		break;
	}
	
	$date_due = ( empty( $ticket['date_due'] ) || 0 == $ticket['date_due'] ) ? '' : dt::date( 'm/d/Y', $ticket['date_due'] );
	$aaData[] = array( '<a href="/tickets/ticket/?tid=' . $ticket['ticket_id'] . '" title="View Ticket">' . format::limit_chars( $ticket['summary'], 55 ) . '</a>', $ticket['name'], $ticket['website'], $priority, $ticket['assigned_to'], dt::date( 'm/d/Y', $ticket['date_created'] ), $date_due );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $ticket_count,
	'iTotalDisplayRecords' => $ticket_count,
	'aaData' => $aaData
) );