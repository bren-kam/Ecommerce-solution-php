<?php
// Instantiate Classes
$fb = new FB( '245607595465926', 'b29a7efe3a1329bae0b425de96acd84b' );

// Get User
$user_id = $fb->user;

// Redirect to correct location
url::redirect('/facebook/contact-us/?app_data=' . url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) ) );