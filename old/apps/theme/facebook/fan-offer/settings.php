<?php
// Instantiate Classes
$fb = new FB( '165348580198324', 'dbd93974b5b4ee0c48ae34cb3aab9c4a' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/fan-offer/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );