<?php
/**
 * @page Add Comment
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
$ajax = new AJAX( $_POST['_nonce'], 'add-comment' );
$ajax->ok( $user, _('You must be signed in to add a reach comment.' ) );

// Insantiate classes
$r = new Reaches;
$rc = new Reach_Comments;
	
// Get reach information
$reach = $r->get( $_POST['rid'], true );
	
// Define variables
$content = stripslashes( $_POST['taReachComment'] );
$status = ( 0 == $reach['status'] ) ? ' (Open)' : ' (Closed)';
$private = ( $_POST['cbPrivate'] ) ? 1 : 0;
	
// Add it to the comments list
$result = $rc->add( $_POST['rid'], 0, $user['user_id'], nl2br( htmlentities( $content ) ), $private, $_POST['a'] );

$ajax->ok( $result, _('There was an error adding your comment.') );
	
// If it's public, send an email to the client, update waiting
if ( !$private ) {
	$ajax->ok( $r->update_waiting( $_POST['rid'], 0 ), _('There was an error updating reach.') );
	fn::mail( $reach['email'], $r->_get_friendly_type( $reach['meta']['type'] ) . ' #' . $_POST['rid'] . $status, "******************* Reply Above This Line *******************\n\n{$content}\n\n" . $r->_get_friendly_type( $reach['meta']['type'] ) . "\n" . $reach['message'], $user['website']['title'] . ' <support@' . DOMAIN . '>', $user['website']['title'] . ' <reaches@imagineretailer.com>' );
}	

$reach_comment = $rc->get_single( $result );
$reach_comment['date'] = dt::date( 'm/d/Y g:ia', $reach_comment['date'] );

// Send the assigned user an email if they are not submitting the comment
if ( $reach['assigned_to_user_id'] != $user['user_id'] ) {
	// Get the user
	$assigned_to_user = $u->get_user( $reach['assigned_to_user_id'] );
	
	// Send email
	fn::mail( $assigned_to_user['email'], 'New Comment on ' . $r->_get_friendly_type( $reach['meta']['type'] ) . ' #' . $_POST['rid'], $user['contact_name'] . ' has posted a new comment on ' . $r->_get_friendly_type( $reach['meta']['type'] ) . ' #' . $_POST['rid'] . ".\n\nhttp://account." . DOMAIN . "/reaches/reach/?rid=" . $_POST['rid'],  $user['website']['title'] . ' <support@' . DOMAIN . '>', $user['website']['title'] . ' <reaches@imagineretailer.com>' );
}

// Add Comment to page via jQuery

$out_comment = "";

$out_comment = '<div class="comment" id="dComment' . $reach_comment['website_reach_comment_id'] . '">';
$out_comment .= '<p class="name">';

if ( '1' == $reach_comment['private'] )  
	$out_comment .= '<img src="/images/icons/reaches/lock.gif" width="11" height="15"0 alt="' . _('Private') . '" class="private" />';

$out_comment .= $reach_comment['name'];
$out_comment .= '<span class="date">' . $reach_comment['date'] . '</span>';

if ( $user['user_id'] == $reach_comment['user_id'] ) {
	$out_comment .= '	<a ajax="1" href="/ajax/reaches/delete-comment/?_nonce=' . nonce::create( 'delete-comment' ) . '&rcid=' . $reach_comment['website_reach_comment_id'] . '" class="delete-comment" title="' .  _('Delete Feedback Comment') . '">';
	$out_comment .= '		<img src="/images/icons/x.png" alt="X" width="16" height="16" />';
	$out_comment .= '	</a>';
}
$out_comment .= '</p>';
$out_comment .= '<p class="message">' . $reach_comment['comment'] . '</p>';
$out_comment .= '<br clear="left" />';
$out_comment .= '</div>';

jQuery( "#dComments" )
	->prepend( $out_comment )
	->sparrow();

jQuery("#taReachComment")
	->val('')
	->blur();
	
// Send response
	
$ajax->add_response( 'jquery', jQuery::getResponse() );
$ajax->respond();
