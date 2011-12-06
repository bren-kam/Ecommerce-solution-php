<?php
// Instantiate Classes
$fb = new FB( '118945651530886', 'ef922d64f1f526079f48e0e0efa47fb7' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/share-and-save/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );