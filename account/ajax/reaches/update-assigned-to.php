<?php
/**
 * @page Update Reach Assigned To
 * @package Imagine Retailer
 */
 
$ajax = new AJAX( $_GET['_nonce'], 'update-assigned-to' );
$ajax->ok( $user, _('You must be signed in to update a reach.') );
$ajax->ok( $user['role'] >= 1, _('You do not have permission to update this reach.') );

// init classes
$r = new Reaches;

$ajax->ok( $r->update_assigned_to( $_GET['rid'], $_GET['val'] ), _('An error occurred while reassigning reach.') );

$rb = $r->get( $_GET['rid'] );

$priorities = array( 
	0 => 'Normal',
	1 => 'High',
	2 => 'Urgent'
);

$new_user = $u->get_user( (int) $_GET['val'] );

$message = 'Hello ' . $new_user['contact_name'] . ",\n\n";
$message .= 'You have been assigned ' . $r->_get_friendly_type( $reach['meta']['type'] ) . ' #' . $_GET['rid'] . ". To view it, follow the link below:\n\n";
$message .= 'http://account.' . DOMAIN . '/reaches/reach/?rid=' . $_GET['rid'] . "\n\n";
$message .= 'Priority: ' . $priorities[$rb['priority']] . "\n\n";
$message .= "Sincerely,\n" . TITLE . " Team";

//fn::mail( $new_user['email'], 'You have been assigned ' . $r->_get_friendly_type( $reach['meta']['type'] ) . ' #' . $_GET['rid'] . ' (' . $priorities[$rb['priority']] . ') - ', $message, TITLE . ' <noreply@' . DOMAIN . '>' );
echo $new_user['email'], 'You have been assigned ' . $r->_get_friendly_type( $reach['meta']['type'] ) . ' #' . $_GET['rid'] . ' (' . $priorities[$rb['priority']] . ') - ', $message, TITLE . ' <noreply@' . DOMAIN . '>';

$ajax->respond();
