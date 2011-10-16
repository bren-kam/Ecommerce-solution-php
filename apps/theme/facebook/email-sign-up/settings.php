<?php
// Instantiate Classes
$fb = new FB( '165553963512320', 'b4957be2dbf78991750bfa13f844cb68' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/email-sign-up/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );