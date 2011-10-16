<?php
/**
 * @page List Websites
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
			case 0:
				$field = 'b.`company_id`';
			break;
			
			case 1:
				$field = 'a.`title`';
			break;
			
			case 2:
				$field = 'b.`store_name`';
			break;
			
			case 3:
				$field = 'b.`contact_name`';
			break;
			
			case 4:
				$field = 'used_products';
			break;
		}
		
		$order_by .= $field . ' ' . $_GET['sSortDir_' . $i] . ', ';
	}
	
	$order_by = substr_replace( $order_by, '', -2 );
}

// Instantiate classes
$w = new Websites;
$c = new Checklists;

/* Filtering  */
$where = '';

// Live websites
if( isset( $_SESSION['websites']['state'] ) )
	$where .= ' AND a.`live` = ' . $_SESSION['websites']['state'];

if( isset( $_SESSION['websites']['search'] ) ) {
	$where .= " AND ( a.`title` LIKE '" . $w->db->escape( $_SESSION['websites']['search'] ) . "%' OR " .
					"a.`domain` LIKE '" . $w->db->escape( $_SESSION['websites']['search'] ) . "%' OR " .
					"b.`contact_name` LIKE '" . $w->db->escape( $_SESSION['websites']['search'] ) . "%' )";
}

// Get websites
$websites = $w->list_websites( $where, $order_by, $limit );
$websites_count = $w->count_websites( $where );

// We must strip slashes for proper display of apostrphes, etc.
foreach( $websites as &$website ){
	foreach( $website as &$slot )
	{
		$slot = stripslashes( $slot);
	}
}

// Get website_ids with incomplete checklists
$incomplete_checklists = $c->incomplete_checklists();

$aaData = array();

if( is_array( $websites ) )
foreach( $websites as $web ) {
	$image = '<img src="/images/icons/companies/' . $web['company_id'] . '.gif" alt="' . $web['company'] . '" title="' . $web['company'] . '" width="24" height="24" />';
	
	$title = '<a href="http://' . $web['domain'] . '/" title="' . $web['domain'] . ' - ' . $web['online_specialist'] . '" target="_blank"><strong>' . $web['title'] . '</strong></a><br />';
	$title .= '<span class="web-actions" style="display: block"><a href="/websites/edit/?wid=' . $web['website_id'] . '" title="' . _('Edit') . ' ' . $web['title'] . '">' . _('Edit') . '</a> | ';
	$title .= '<a href="/websites/control/?wid=' . $web['website_id'] . '" title="' . _('Control') . ' ' . $web['title'] . '" target="_blank">' . _('Control Website') . '</a> | ';
	$title .= '<a href="/users/control/?uid=' . $web['user_id'] . '" title="' . _('Control User') . '" target="_blank">' . _('Control User') . '</a> | ';
    $title .= '<a href="/websites/notes/?wid=' . $web['website_id'] . '" title="' . _('Notes') . '" target="_blank">' . _('Notes') . '</a>';
	
	if( isset( $incomplete_checklists[$web['website_id']] ) )
		$title .= ' | <a href="/checklists/view/?cid=' . $incomplete_checklists[$web['website_id']] . '" title="' . _('Checklists') . '" target="_blank">' . _('Checklist') . '</a>';
	
	$title .= '</span>';
	$aaData[] = array( $image, $title, $web['store_name'], '<a href="/users/edit/?uid=' . $web['user_id'] . '" title="' . _('Edit User') . '">' . $web['contact_name'] . '</a>', $web['used_products'] . '/' . $web['products'] );
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $websites_count,
	'iTotalDisplayRecords' => $websites_count,
	'aaData' => $aaData
) );