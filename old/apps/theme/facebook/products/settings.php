<?php
// Instantiate Classes
$fb = new FB( '163636730371197', '3dbe8bc58cf03523ad51603654ca50a6' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/products/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );