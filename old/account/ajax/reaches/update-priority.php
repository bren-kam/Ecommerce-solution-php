<?php
/**
 * @page Update Priority
 * @package Grey Suit Retail
 */
 
$ajax = new AJAX( $_GET['_nonce'], 'update-priority' );
$ajax->ok( $user, _('You must be signed in to update a reach.') );
$ajax->ok( $user['role'] >= 1, _('You do not have permission to update this reach.') );

// create class
$r = new Reaches;

// do it to it
$ajax->ok( $r->update_priority( $_GET['rid'], $_GET['val'] ), _('There was an error updating reach priority') );

$ajax->respond();
