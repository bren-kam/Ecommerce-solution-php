<?php
// Start timer
$timer = new Timer();

// Check slave
require '/gsr/systems/db.slave.php';
$m = new mysqli( $db_host, $db_username, $db_password, $db_name );
$message = ( $m->connect_error ) ? 'FAIL' : 'OK';

// Mark the page as XML
header::type('xml');

// Create XML
$xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
$xml->addChild( 'status', $message );
$xml->addChild( 'response_time', round( $timer->stop() * 1000000, 3 )  );

// Spit out XML
echo $xml->asXML();
?>