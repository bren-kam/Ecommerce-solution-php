<?php
// Start timer
$timer = new Timer();

// Check slave
require '/gsr/systems/db.master.php';
$m = new mysqli( DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME );
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