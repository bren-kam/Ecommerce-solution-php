<?php
global $user;
if ( $user['role'] < 10 ) return false;

$e = new Email_Marketing();
$e->transfer_emails( 156, 287 );


echo "1"; exit;
?>