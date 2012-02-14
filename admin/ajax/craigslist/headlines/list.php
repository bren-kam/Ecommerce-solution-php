<?php
/**
 * @page List Craigslist Headlines
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
			case 1:
				$field = 'a.`headline`';
			break;		

			case 2:
				$field = 'b.`name`';
			break;

			case 3:
				$field = 'a.`date_created`';
			break;
		}
		
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}

// Instantiate classes
$c = new Craigslist;

/* Filtering  */
$where = '';

if ( !empty( $_GET['sSearch'] ) ) {
	$where .= " AND ( a.`headline` LIKE '%" . $c->db->escape( $_GET['sSearch'] ) . "%' ";
	$where .= " OR b.`name` LIKE '" . $c->db->escape( $_GET['sSearch'] ) . "%' ) ";
}

// Get Craigslist Templates
$headlines = $c->list_craigslist_headlines( $where, $order_by, $limit );
$headline_count= $c->count_craigslist_headlines( $where );

$aaData = array();

if ( is_array( $headlines ) )
foreach ( $headlines as $h ) {
	$title = format::limit_chars( $h['headline'] );
	$title .= '<div class="actions">';
	$title .= '<a href="/craigslist/headlines/add-edit/?chid=' . $h['craigslist_headline_id'] . '" title="' . _('Edit Headline') . '">' . _('Edit') . '</a> | ';
	$title .= '<a href="#" id="aDelete' . $h['craigslist_headline_id'] . '" class="delete-headline" title="' . _('Delete Headline') . '">' . _('Delete') . '</a>';
	$title .= '</div>';

    $date = new DateTime( $h['date_created'] );
	$aaData[] = array( $title, $h['category'], $date->format( 'F jS, Y') );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $headline_count,
	'iTotalDisplayRecords' => $headline_count,
	'aaData' => $aaData
) );