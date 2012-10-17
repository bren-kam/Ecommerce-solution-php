<?php
/**
 * @page PHP Unit Testing Page
 * @package Grey Suit Retail
 */

//global $user;

//if ( !$user )
	//login();

require 'PHPUnit/Autoload.php';
	
// Test Studio98 Library
require '/home/develop4/public_html/s98lib/tests/date-time-test.php';


$suite  = new PHPUnit_TestSuite("date_timeTest");
$result = PHPUnit::run($suite);

echo $result->toString();
