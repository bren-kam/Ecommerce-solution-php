<?php
// Instantiate Classes
$fb = new FB( '233746136649331', '298bb76cda7b2c964e0bf752cf239799' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/about-us/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );