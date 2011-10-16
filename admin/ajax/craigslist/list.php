<?php
/**
 * @page List Craigslist Templates
 * @package Imagine Retailer
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if( !$user ) {
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
		switch( $_GET['iSortCol_' . $i] ) {
			default:
			case 1:
				$field = 'a.`title`';
			break;		

			case 2:
				$field = 'a.`description`';
			break;				

			case 3:
				$field = '`category_name`';
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

if( isset( $_SESSION['craigslist']['search'] ) ) {
	$where .= ' WHERE (';
					 
	switch( $_SESSION['craigslist']['category'] ) { //$_POST['t'] ){
		default:
		case 'title':
			$where .= 'a.`title`';
			break;
		case 'content':
			$where .= 'a.`description`';
			break;
		case 'category':
			$where .= 'b.`name`';
			break;
	}
	$where .= " LIKE '%" . $c->db->escape( $_SESSION['craigslist']['search'] ) . "%' ) ";
}

// Get Craigslist Templates
$craigslists = $c->list_craigslist( $where, $order_by, $limit );
$craigslist_count= $c->count_craigslist( $where );

$aaData = array();

if( is_array( $craigslists ) )
foreach( $craigslists as $template) {	
	$title = '<strong>' . $template['title'] . '</strong><br/>';
	$title .= '<span class="web-actions" style="display:block">';
	$title .= '<a href="/craigslist/add-edit/?cid=' . $template['craigslist_template_id'] . '" title="Edit ' . $template['title'] . '">Edit</a> | ';
	$title .= '<a href="#" id="aDelete' . $template['craigslist_template_id'] . '" class="delete-craigslist" title="Delete ' . $template['title'] . '">Delete</a>';
	$title .= '</span>';
	$aaData[] = array( $title, $template['description'], $template['category_name'], $template['date_created'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $craigslist_count,
	'iTotalDisplayRecords' => $craigslist_count,
	'aaData' => $aaData
) );