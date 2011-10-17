<?php
/**
 * @page List Checklists
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
				$field = 'a.`type`';
			break;

			case 3:
				$field = 'a.`date_created`';
			break;
		}
		
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}

// Instantiate class
$c = new Checklists;

/* Filtering  */
$where = ' AND a.`checklist_id` IN ( SELECT `checklist_id` FROM `checklist_website_items` WHERE `checked` = 0 )';

if ( !empty( $_GET['sSearch'] ) )
	$where .= " AND b.`title` LIKE '" . $c->db->escape( $_GET['sSearch'] ) . "%'";

// Get websites
$checklists = $c->list_checklists( $where, $order_by, $limit );
$checklists_count = $c->count_checklists( $where );

$aaData = array();

if ( is_array( $checklists ) )
foreach ( $checklists as $cl ) {
	// Determined which color should be used for days left
	switch ( $cl['days_left'] ) {
		case ( $cl['days_left'] < 10 ):
			$color = 'red';
		break;
			
		case ( $cl['days_left'] < 20 ):
			$color = 'orange';
		break;
		
		default:
			$color = 'green';
		break;
	}
		
	$aaData[] = array( '<span class="' . $color . '">' . $cl['days_left'] . '</span>',  $cl['title'] . '<br /><a href="/checklists/view/?cid=' . $cl['checklist_id'] . '" title="View Checklist">View</a>', $cl['type'], $cl['date_created'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $checklists_count,
	'iTotalDisplayRecords' => $checklists_count,
	'aaData' => $aaData
) );