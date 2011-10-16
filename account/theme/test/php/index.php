<?php
/**
 * @page PHP Unit Testing Page
 * @package Imagine Retailer
 */

//global $user;

//if( !$user )
	//login();

require 'PHPUnit/Autoload.php';
	
// Test Studio98 Library
require '/home/imaginer/public_html/s98lib/tests/date-time-test.php';


$suite  = new PHPUnit_TestSuite("date_timeTest");
$result = PHPUnit::run($suite);

echo $result->toString();
