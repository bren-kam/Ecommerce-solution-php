<?php
/**
 * @page List Tickets
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


// Instantiate classes
$r = new Reaches;
$dt = new Data_Table();

// Role based settings
$role = ( $user['role'] < 5 && $user['role'] >= 1 ) ? " AND a.`status` = 0 AND a.`waiting` = 1 " : "";

// Set variables
$dt->order_by( '`name`', 'b.`email`', '`assigned_to`', 'a.`status`', 'a.`priority`', 'a.`date_created`' );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] . $role );
$dt->search( array( 'name' => false, 'b.`email`' => false, 'assigned_to' => false ) );

$reaches = $r->list_reaches( $dt->get_variables() );
$dt->set_row_count( $r->count_reaches( $dt->get_where() ) );

/* set up the data */
$priorities = array( 
	0 => _('Normal'),
	1 => _('High'),
	2 => _('Urgent')
);

$statuses = array( 
	0 => _('Open'),
	1 => _('Closed')
);

$data = array();

foreach ( $reaches as $reach ) {
	$data[] = array(
		'<a href="/reaches/reach/?rid=' . $reach['website_reach_id'] . '">' . $reach['name'] . '</a>'
        , $reach['email']
		, $reach['assigned_to']
		, $statuses[ (int) $reach['status'] ]
		, $priorities[ (int) $reach['priority'] ]
		, dt::date( 'm/d/Y g:ia', $reach['date_created'] ) 
	);
	
}



echo $dt->get_response( $data ); 