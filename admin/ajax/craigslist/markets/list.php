<?php
/**
 * @page List Craigslist Markets
 * @package Grey Suit Retail
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
				$field = 'market';
			break;		

			case 2:
				$field = '`date_created`';
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
	$where .= " AND market LIKE '%" . $c->db->escape( $_GET['sSearch'] ) . "%' ) ";
}

// Get Craigslist Templates
$markets = $c->list_craigslist_markets( $where, $order_by, $limit );
$market_count= $c->count_craigslist_markets( $where );

$aaData = array();

if ( is_array( $markets ) )
foreach ( $markets as $m ) {
	$title = $m['market'];
	$title .= '<div class="actions">';
	$title .= '<a href="/craigslist/markets/add-edit/?cmid=' . $m['craigslist_market_id'] . '" title="' . _('Edit Market') . '">' . _('Edit') . '</a> | ';
	$title .= '<a href="#" id="aDelete' . $m['craigslist_market_id'] . '" class="delete-market" title="' . _('Delete Market') . '">' . _('Delete') . '</a>';
	$title .= '</div>';

    $date = new DateTime( $m['date_created'] );
	$aaData[] = array( $title, $date->format( 'F jS, Y') );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $market_count,
	'iTotalDisplayRecords' => $market_count,
	'aaData' => $aaData
) );