<?php
// Instantiate Class
$w = new Websites;

// Get the user they are trying to control
$new_user = $u->get_user( $_GET['uid'] );

// Make sure they're not trying to control someone with the same role or a higher role
if( $user['role'] <= $new_user['role'] )
	url::redirect( $_SERVER['HTTP_REFERER'] );

// Get the websites that user controls
$websites = $w->get_user_websites( $_GET['uid'] );

$auth_cookie = ( security::is_ssl() ) ? AUTH_COOKIE : SECURE_AUTH_COOKIE;

set_cookie( $auth_cookie, base64_encode( security::encrypt( $new_user['email'], security::hash( COOKIE_KEY, 'secure-auth' ) ) ), 172800 );
set_cookie( 'wid', $websites[0]['website_id'], 172800 ); // 2 days
set_cookie( 'action', base64_encode( security::encrypt( 'bypass', ENCRYPTION_KEY ) ), 172800 ); // 2 days

if( stripos( $_SERVER['HTTP_REFERER'], 'admin2' ) ) {
	$url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : 'account2.imagineretailer.com' ) . '/';
} else {
	$url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : 'account.imagineretailer.com' ) . '/';
}

url::redirect( $url );
?>