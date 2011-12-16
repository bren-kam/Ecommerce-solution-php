<?php
/**
 * @page Update Scheduled Emails
 * @package Imagine Retailer
 */

$e = new Emails;
$e->update_scheduled_emails();

$t = new Tickets;
$t->clean_uploads();

// Send Autoposts
$sm = new Social_Media;

$posts = $sm->get_posting_posts();

if ( is_array( $posts ) ) {
	$fb = new FB( '268649406514419', '6ca6df4c7e9d909a58d95ce7360adbf3' );
	
	$sm_posting_post_ids = array();
	
	foreach ( $posts as $p ) {
		$fb->setAccessToken( $p['access_token'] );
		
		// Information:
		// http://developers.facebook.com/docs/reference/api/page/#posts
		if ( $fb->api( $p['fb_page_id'] . '/feed', 'POST', array( 'message' => $p['post'], 'link' => $p['link'] ) ) )
			$sm_posting_post_ids[] = $p['sm_posting_post_id'];
	}
	
	// Mark as posted
	$sm->complete_posting_posts( $sm_posting_post_ids );
}
?>