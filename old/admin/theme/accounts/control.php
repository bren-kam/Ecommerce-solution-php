<?php
global $user;

// Not anyone can get here
if ( !$user )
	url::redirect( '/' );

set_cookie( 'wid', $_GET['wid'], 172800 ); // 2 days
set_cookie( 'action', base64_encode( security::encrypt( 'bypass', ENCRYPTION_KEY ) ), 172800 ); // 2 days

$url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : str_replace( 'admin', 'account', $_SERVER['HTTP_HOST'] ) );
url::redirect( $url );
?>