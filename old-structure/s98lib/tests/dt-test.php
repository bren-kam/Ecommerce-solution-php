<?php
/**
 * PHP Unit Test for date-time class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class dtTest extends PHPUnit_Framework_TestCase {
	/**
     * Make sure the dates work identically
     */
	public function testDate() {
		$this->assertEquals( dt::date('Y-m-d H:i:s'), date('Y-m-d H:i:s') );
	}
	
	/**
     * Testing Nice Names
     */
	public function testDateNiceNames() {
		$this->assertEquals( dt::date('datetime'), date('Y-m-d H:i:s') );
	}
	
	/**
     * Make sure that we can just do "now"
     *
     * @depends testDate
     */
	public function testNow() {
		$this->assertEquals( dt::now(), date('Y-m-d H:i:s') );
	}

    /**
     * Testing seconds to time
     */
    public function testSecToTime() {
        // 3,732 seconds equals 1:02:12
        $this->assertEquals( '01:02:12', dt::sec_to_time( 3732 ) );
    }

    /**
     * Testing Adjust Timezone
     */
    public function testAdjustTimezone() {
        // Setup variables
        $date = '2012-07-02 00:38:35';
        $timezone = 'America/Los_Angeles';
        $new_timezone = 'America/Chicago';

        // Adjust the date to chicago, 2 hours ahead
        $adjusted_date = dt::adjust_timezone( $date, $timezone, $new_timezone, 'F jS, Y / g:i:s A');

        // Should be adjusted with the given format
        $this->assertEquals( 'July 2nd, 2012 / 2:38:35 AM', $adjusted_date );
    }
}