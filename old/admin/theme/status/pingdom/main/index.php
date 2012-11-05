<?php
// Start timer
$timer = new Timer();

// Mark the page as XML
header::type('xml');

// Create XML
$xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
$xml->addChild( 'status', 'OK' );
$xml->addChild( 'response_time', round( $timer->stop() * 1000000, 3 )  );

// Spit out XML
echo $xml->asXML();
?>