<?php
// Instantiate Classes
$fb = new FB( '179756052091285', '8a76794c39b8992c21f706c9258c8bbb' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/analytics/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );