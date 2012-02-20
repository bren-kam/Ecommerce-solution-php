<?php
/**
 * @page Update Reach Status
 * @package Imagine Retailer
 */
// Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'update-status' );
$ajax->ok( $user, _('You must be signed in to update a reach.') );
$ajax->ok( $user['role'] >= 1, _('You do not have permission to update this reach.') );

$r = new Reaches;

$ajax->ok( $r->update_status( $_GET['rid'], $_GET['val'] ), _('An error occured while updating status.') );
	
$ajax->respond();


