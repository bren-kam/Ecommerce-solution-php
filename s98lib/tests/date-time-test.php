<?php
/**
 * PHP Unit Test for date-time class
 *
 * @package Studio98 Framework
 * @since 1.0
 */

// Require main test
require '/home/develop4/public_html/s98lib/classes/date-time.php';

class date_timeTest extends PHPUnit_Framework_TestCase {
	// Make sure that the two functions work the same
	public function testDuplicateFunction() {
		$this->assertEquals( dt::date('Y-m-d H:i:s'), date('Y-m-d H:i:s') );
	}
	
	// Make sure that the nice names work
	public function testNiceNames() {
		$this->assertEquals( dt::date('datetime'), date('Y-m-d H:i:s') );
	}
	
	// Make sure the now functin works
	public function testNow() {
		$this->assertEquals( dt::now(), date('Y-m-d H:i:s') );
	}
	
	// Make sure that it can convert it to time
	public function testSecondsToTime() {
		$this->assertEquals( dt::sec_to_time( 3600 ), date('1:00') );
	}
}