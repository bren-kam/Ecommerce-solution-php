<?php
/**
 * PHP Unit Test for base-cache class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class Base_CacheTest extends PHPUnit_Framework_TestCase {
    /**
     * @var Base_cache
     */
    protected $bc;

    /**
     * Setup the test
     */
    protected function setUp() {
        // Instantiate Base Cache
        $this->bc = new Base_Cache();
    }

    /**
     * Test Get function
     */
    public function testGetA() {
        // Make sure it returns false
        $this->assertFalse( $this->bc->get('standard-color') );
    }

    /**
     * Test Add function
     */
    public function testAddA() {
        // Set the standard color
        $standard_color = $this->bc->add( 'standard-color', 'silver' );

        // Make sure it returned the right value
        $this->assertEquals( 'silver', $standard_color );
    }

    /**
     * Test Add function - overwriting
     *
     * @depends testAddA
     */
    public function testAddB() {
        // Set the standard color
        $this->bc->add( 'standard-color', 'silver' );
        $standard_color = $this->bc->add( 'standard-color', 'blue' );

        // Make sure it returned the right value
        $this->assertFalse( $standard_color );
    }

    /**
     * Test Get function
     *
     * @depends testAddA
     */
    public function testGetB() {
        // Set the standard color
        $this->bc->add( 'standard-color', 'silver' );

        // Get from cache
        $standard_color = $this->bc->get('standard-color');

        // Make sure it returned the right value
        $this->assertEquals( 'silver', $standard_color );
    }

    /**
     * Test Delete
     *
     * @depends testAddA
     * @depends testGetB
     */
    public function testDelete() {
        // Set the standard color
        $this->bc->add( 'standard-color', 'silver' );

        // Delete it
        $this->bc->delete( 'standard-color' );

        // Make sure it can't be found
        $this->assertFalse( $this->bc->get('standard-color') );
    }

    /**
     * Test Flushing the cache
     *
     * @depends testAddA
     * @depends testGetB
     */
    public function testFlush() {
        // Set the standard color
        $this->bc->add( 'standard-color', 'silver' );
        $this->bc->add( 'standard-weight', "5lb" );

        // Flush the cache
        $this->bc->flush();

        // Get them -- should return false
        $standard_color = $this->bc->get('standard-color');
        $standard_weight = $this->bc->get('standard-weight');

        // Make sure they can't be found
        $this->assertTrue( !$standard_color && !$standard_weight );
    }

    /**
     * Test Replacing - Overwriting Nothing
     */
    public function testReplaceA() {
        // Set the standard color
        $standard_color = $this->bc->replace( 'standard-color', 'silver' );

        $this->assertFalse( $standard_color );
    }

    /**
     * Test Replacing - Overwriting
     */
    public function testReplaceB() {
        // Set the standard color
        $this->bc->add( 'standard-color', 'blue' );
        $success = $this->bc->replace( 'standard-color', 'silver' );

        $this->assertTrue( $success );
    }

   	/**
     * Get Cache Misses
     */
    public function testGetMissesA() {
        $this->assertEquals( 0, $this->bc->get_misses() );
    }

	/**
     * Get Cache Misses - with an attempted grabbed
     *
     * @depends testGetB
     */
    public function testGetMissesB() {
        // Search for something that doesn't exist
        $this->bc->get('once-upon-a-time');

        // We should now have a miss
        $this->assertEquals( 1, $this->bc->get_misses() );
    }


	/**
     * Get Cache Hits - Defaults to 0
     */
    public function testGetHitsA() {
        $this->assertEquals( 0, $this->bc->get_hits() );
    }

	/**
     * Get Cache Hits - with something grabbed
     *
     * @depends testAddA
     * @depends testGetB
     */
    public function testGetHitsB() {
        // Add something
        $this->bc->add( 'standard-color', 'silver' );

        // Search for something that does
        $this->bc->get('standard-color');

        $this->assertEquals( 1, $this->bc->get_hits() );
    }
}