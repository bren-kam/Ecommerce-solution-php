<?php
/**
 * @page List Craigslist Accounts
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
				$field = 'a.`title`';
			break;

			case 2:
				$field = 'plan';
			break;

			case 2:
				$field = 'Markets';
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
	$where .= " AND ( a.`title` LIKE '%" . $c->db->escape( $_GET['sSearch'] ) . "%'";
	$where .= " OR c.`value` LIKE '" . $c->db->escape( $_GET['sSearch'] ) . "%'";
	$where .= " OR d.`value` LIKE '" . $c->db->escape( $_GET['sSearch'] ) . "%' )";
}

// Get Craigslist Accounts
$accounts = $c->list_craigslist_accounts( $where, $order_by, $limit );
$account_count = $c->count_craigslist_accounts( $where );

$aaData = array();

if ( is_array( $accounts ) )
foreach ( $accounts as $a ) {
	$title = $a['title'];
	$title .= '<div class="actions">';
	$title .= '<a href="/craigslist/accounts/add-edit/?wid=' . $a['website_id'] . '" title="' . _('Edit Account') . '">' . _('Edit') . '</a> | ';
	$title .= '<a href="/craigslist/accounts/link-market/?wid=' . $a['website_id'] . '" title="' . _('Link Market') . '">' . _('Link Market') . '</a>';
	$title .= '</div>';

	$aaData[] = array( $title, $a['plan'], $a['markets'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $account_count,
	'iTotalDisplayRecords' => $account_count,
	'aaData' => $aaData
) );