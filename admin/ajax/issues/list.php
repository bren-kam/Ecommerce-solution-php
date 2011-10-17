<?php
/**
 * @page List Issues
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

$is = new Issues;

/* Paging */
$limit = ( isset( $_GET['iDisplayStart'] ) ) ? intval( $_GET['iDisplayStart'] ) . ', ' . intval( $_GET['iDisplayLength'] ) : 1000;

$order_by = '';

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) ) {
	for ( $i = 0 ;$i < intval( $_GET['iSortingCols'] ); $i++ ) {
		switch (  $_GET['iSortCol_' . $i] ) {
			default:
			case 0:
				$field = '`message`';
			break;

			case 1:
				$field = '`occurrences`';
			break;

			case 2:
				$field = '`priority`';
			break;
			
			case 3:
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
	$where .= " AND ( `message` LIKE '%" . $is->db->escape( $_GET['sSearch'] ) . "%' )";
}

// Grab only the right status
if ( isset( $_SESSION['issues']['status'] ) )
	$where .= ' AND `status` = ' . $_SESSION['issues']['status'];

$issues = $is->list_issues( $limit, $where, $order_by );
$issue_count = $is->count_issues( $where );

$aaData = array();

if ( is_array( $issues ) )
foreach ( $issues as $issue ) {
	switch ( $issue['priority'] ) {
		case 1:
			$priority = '<span class="low">LOW</span>';
		break;
		
		case 2:
			$priority = '<span class="normal">NORMAL</span>';
		break;
		
		case 3:
			$priority = '<span class="high">HIGH</span>';
		break;
	}
	
	$aaData[] = array( 
		'<a href="/issues/issue/?ik=' . $issue['issue_key'] . '" title="Issue Key">' . format::limit_chars( strip_tags( $issue['message'] ), 100 ) . '</a>'
		, $issue['occurrences']
		, $priority
		, dt::date( 'm/d/Y', $issue['date_created'] )
	);
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $issue_count,
	'iTotalDisplayRecords' => $issue_count,
	'aaData' => $aaData
) );