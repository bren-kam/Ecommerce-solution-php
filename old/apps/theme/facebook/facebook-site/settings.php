<?php
// Instantiate Classes
$fb = new FB( '114243368669744', 'bad9a248b9126bdd62604ccd909f8d2d' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/facebook-site/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );