<?php
/**
 * PHP Unit Test for urlclass
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class timerTest extends PHPUnit_Framework_TestCase {
    /**
     * @var timer
     */
    protected $timer;

    /**
     * Setup the test
     */
    protected function setUp() {
        // Instantiate timer
        $this->timer = new timer();
    }

    /**
     * Test starting the timer - B
     */
	public function testStart() {
        $timer = new timer( false );

        // It should return a starting number
        $this->assertGreaterThan( 0, $timer->start() );
	}

    /**
     * Test to see if we can stop the timer
     *
     * @depends testStart
     */
    public function testStop() {
        // It should return the elapse timed (not testing yet)
        $this->assertGreaterThan( 0, $this->timer->stop() );
    }

    /**
     * Checks to see if we can get the start
     *
     * @depends testStart
     */
    public function testGetStart() {
        // It should return an int
        $this->assertGreaterThan( 0, $this->timer->get_start() );
    }

    /**
     * Checks to see if we can get the stop
     *
     * @depends testStop
     */
    public function testGetStop() {
        // Stop the timer
        $this->timer->stop();

        // It should return an int
        $this->assertGreaterThan( $this->timer->get_start(), $this->timer->get_stop() );
    }

    /**
     * Checks to see if we can get the elapsed time
     *
     * @depends testStop
     */
    public function testGetElapsed() {
        // Stop the timer
        $this->timer->stop();

        // It should return an int
        $this->assertEquals( $this->timer->get_elapsed(), $this->timer->get_stop() - $this->timer->get_start() );
    }

    /**
     * Make sure that time is valid
     *
     * @depends testGetElapsed
     */
    public function testElapsed() {
        // Stop the timer
        $this->timer->stop();

        // Get the elapsed time
        $elapsed_time = $this->timer->get_elapsed();

        // It should be less than a second
        $this->assertTrue( $elapsed_time > 0 && $elapsed_time < 1 );
    }
}