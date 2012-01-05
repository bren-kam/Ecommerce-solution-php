<?php
/**
 * @page List Websites
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

$emails = array( array( 'address' => 'test@test.com', 'pw' => 'jq293orkjl', 'usage' => 22, 'quota' => '256' ), array( 'address' => 'test2@test.com', 'pw' => 'k2j3KKK', 'usage' => 22, 'quota' => '512' ), array( 'address' => 'test3@test.com', 'pw' => 'kasjl234', 'usage' => 22, 'quota' => '1024' ) );

$aaData = array();

$slot = 1;

if ( is_array( $emails ) )
foreach ( $emails as $email ) {
	$info = '<input type="hidden" id="hAddress' . $slot . '" value="' . $email['address'] . '"/>
			 <input type="hidden" id="hPw' . $slot . '" value="' . $email['pw'] . '"/>
			 <input type="hidden" id="hQuota' . $slot . '" value="' . $email['quota'] . '"/>';
	$title = '<div id="dEmail' . $slot . '"><strong>' . $email['address'] . '</strong></div>';
	
	$functions = '<a class="edit-email-address" id="aEdit' . $slot . '" href="#" title="' . _('Edit') . '">' . _('Edit') . '</a> | ';
	$functions .= '<a class="delete-email" id="aDelete' . $slot . '" href="#" title="' . _('Delete') . '">' . _('Delete') . '</a>';	
	
	$quota = ( ( $email['usage'] ) ? $email['usage'] : '?' ) . "MB / " . ( ( $email['quota'] ) ? $email['quota'] : '?' ) . "MB";
	
	$aaData[] = array( $info . $title, $quota, $functions );
	
	$slot++;
}

echo json_encode( array( 
	'sEcho' => intval( $_GET['sEcho'] ),
	'iTotalRecords' => $websites_count,
	'iTotalDisplayRecords' => $websites_count,
	'aaData' => $aaData
) );