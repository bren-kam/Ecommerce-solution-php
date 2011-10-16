<?php
// Instantiate Classes
$fb = new FB( '113993535359575', '16937c136a9c5237b520b075d0ea83c8' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/sweepstakes/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );