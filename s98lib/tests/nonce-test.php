<?php
/**
 * PHP Unit Test for nonce
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class nonceTest extends PHPUnit_Framework_TestCase {
    /**
     * Test Nonce Create method
     */
    public function testCreate() {
        // Create the nonce
        $nonce = nonce::create('test');

        $this->assertEquals( 10, strlen( $nonce ) );
    }

    /**
     * Test to verify a nonce was created in the last 6 hours
     *
     * @depends testCreate
     */
    public function testVerifyA() {
        // Create the nonce
        $nonce = nonce::create('test');

        // Check to make sure it knows it was created within the last 6 hours
        $this->assertEquals( 1, nonce::verify( $nonce, 'test' ) );
    }

    /**
     * Test to verify a nonce will return false if incorrect
     *
     * @depends testCreate
     */
    public function testVerifyB() {
        // Create the nonce
        $nonce = nonce::create('test');

        // Check to make sure it is false
        $this->assertFalse( nonce::verify( $nonce, 'different' ) );
    }

    /**
     * Test creating a nonce field
     *
     * @depends testCreate
     */
    public function testField() {
        // Get the field
        $field = nonce::field( 'test', '_nonce', false );

        $this->assertTrue( stristr( $field, '<input type="hidden"' ) !== false );
    }

    /**
     * Test creating a nonce URL
     *
     * @depends testCreate
     */
    public function testURL() {
        // Get the url
        $url = nonce::url( '/testing/', 'zebras' );

        $this->assertTrue( stristr( $url, '/testing/?_nonce=' ) !== false );
    }
}