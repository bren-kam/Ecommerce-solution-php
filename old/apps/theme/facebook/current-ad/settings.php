<?php
// Instantiate Classes
$fb = new FB( '186618394735117', 'd4cbf0c45ed772cf1ca0d98e0adb1383' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/current-ad/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );