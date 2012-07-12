<?php
/**
 * PHP Unit Test for url class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class urlTest extends PHPUnit_Framework_TestCase {
    /**
     * Make sure that we can grab the proper domain from a string
     */
	public function testDomainA() {
        // Starting URL
        $url = 'http://www.studio98.com/';

        // Get the domain
        $domain = url::domain( $url );

        // Make sure they're equal
		$this->assertEquals( 'www.studio98.com', $domain );
	}

	/**
     * Make sure we can grab the proper domain (not including subdomain) from a string
     */
	public function testDomainB() {
        // Starting URL
        $url = 'http://www.studio98.com/';

        // Get the domain
        $domain = url::domain( $url, false );

        // Make sure they're equal
		$this->assertEquals( 'studio98.com', $domain );
	}

    /**
     * Make sure we can grab the proper subdomain
     *
     * @depends testDomainA
     * @depends testDomainB
     */
	public function testSubdomain() {
        // Starting URL
        $url = 'http://www.studio98.com/';

        // Get the domain
        $subdomain = url::subdomain( $url );

        // Make sure they're equal
		$this->assertEquals( 'www', $subdomain );
	}

	/**
     * Make sure we can encode something properly
     */
	public function testEncode() {
        $array = array(
            'message' => 'Hello World!'
        );

        $encoded_string = url::encode( $array );

        // Make sure they're equal
		$this->assertEquals( 'eNpLtDK0qi62MrdSyk0tLk5MT1WyLrYyNLJS8kjNyclXCM8vyklRVLKuBVww_s4Mhw,,', $encoded_string );
	}

    /**
     * Test Decode
     */
    public function testDecode() {
        $encoded_string = 'eNpLtDK0qi62MrdSyk0tLk5MT1WyLrYyNLJS8kjNyclXCM8vyklRVLKuBVww_s4Mhw,,';

        $decoded_data = url::decode( $encoded_string );

        // Make sure they're equal
		$this->assertEquals( 'Hello World!', $decoded_data['message'] );
    }

    /**
     * Make sure we can encode and decode data and come up with the same result
     */
    public function testEncodeDecode() {
        $random_string = md5( rand() );
        $decoded_string = url::decode( url::encode( $random_string ) );

        // Make sure they're equal
		$this->assertEquals( $random_string, $decoded_string );
    }

    /**
     * Testing adding a query argument
     */
    public function testAddQueryArg() {
        $url = 'http://www.studio98.com/';
        $new_url = url::add_query_arg( 'test', 'true', $url );

        // Make sure they're equal
		$this->assertEquals( 'http://www.studio98.com/?test=true', $new_url );
    }

    /**
     * Testing adding multing query arguments
     */
    public function testAddQueryArgArray() {
        $url = 'http://www.studio98.com/?still=here#success';
        $new_url = url::add_query_arg( array( 'test' => 'true', 'help' => 'needed' ), $url );

        // Make sure they're equal
		$this->assertEquals( 'http://www.studio98.com/?still=here&test=true&help=needed#success', $new_url );
    }

    /**
     * Testing removing a query argument
     */
    public function testRemoveQueryArg() {
        $url = 'http://www.studio98.com/?test=true';
        $new_url = url::remove_query_arg( 'test', $url );

        // Make sure they're equal
		$this->assertEquals( 'http://www.studio98.com/', $new_url );
    }

    /**
     * Testing removing multiple query argument
     */
    public function testRemoveQueryArgArray() {
        $url = 'http://www.studio98.com/?test=true&help=needed&still=here';
        $new_url = url::remove_query_arg( array( 'test', 'help' ), $url );

        // Make sure they're equal
		$this->assertEquals( 'http://www.studio98.com/?still=here', $new_url );
    }
}