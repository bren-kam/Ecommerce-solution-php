<?php
exit;

global $user;

if ( !$user ) exit;
if ( $user['role'] < 10 ) exit;

$e = new Email_Marketing();
$e->decode_templates();

?>