<?php
global $user;

// Not anyone can get here
if ( !$user )
	url::redirect( '/' );

set_cookie( 'wid', $_GET['wid'], 172800 ); // 2 days
set_cookie( 'action', base64_encode( security::encrypt( 'bypass', ENCRYPTION_KEY ) ), 172800 ); // 2 days

// If it's admin2, redirect to account2 instead
if ( stripos( $_SERVER['HTTP_REFERER'], 'admin2' ) ) {
	$url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : 'account2.imagineretailer.com' ) . '/';
} else {
	$url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : 'account.development.imagineretailer.com' ) . '/';
}
url::redirect( $url );
?>