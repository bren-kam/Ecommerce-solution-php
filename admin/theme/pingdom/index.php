<?php
$timer = new Timer();

header::type('xml');
$xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
$xml->addChild( 'status', 'OK' );
$xml->addChild( 'response_time', round( $timer->stop() * 1000000, 3 )  );

echo $xml->asXML();
?>