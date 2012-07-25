<?php
$timer = new Timer();

header::type('xml');
$xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
$xml->addChild( 'status', 'OK' );
$xml->addChild( 'response_time', $timer->stop() );

echo $xml->asXML();
?>