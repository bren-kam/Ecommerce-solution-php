<?php
/**
 * PHP Unit Test for security
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class securityTest extends PHPUnit_Framework_TestCase {
    /**
     * Test If SSL is valid
     */
    public function testIsSSLA() {
        // To revert
        $https = ( isset( $_SERVER['HTTPS'] ) ) ? $_SERVER['HTTPS'] : NULL;

        // If it's off, it should not show ssl
        $_SERVER['HTTPS'] = 'off';

        // Should NOT be SSL
        $this->assertFalse( security::is_ssl() );

        // Revert
        $_SERVER['HTTPS'] = $https;
    }

    /**
     * Test If SSL is valid
     */
    public function testIsSSLB() {
        // To revert
        $https = ( isset( $_SERVER['HTTPS'] ) ) ? $_SERVER['HTTPS'] : NULL;

        // If it's on, it should be ssl
        $_SERVER['HTTPS'] = 'on';

        // Should be SSL!
        $this->assertTrue( security::is_ssl() );

        // Revert
        $_SERVER['HTTPS'] = $https;
    }

    /**
     * Need to also check ports
     */
    public function testIsSSLC() {
        // To revert
        $original_port = ( isset( $_SERVER['SERVER_PORT'] ) ) ? $_SERVER['SERVER_PORT'] : NULL;

        // If it's HTTPS is not set, but the port is, SSL
        $_SERVER['SERVER_PORT'] = 443;

        // Should be SSL!
        $this->assertTrue( security::is_ssl() );

        // Revert
        $_SERVER['SERVER_PORT'] = $original_port;
    }

    /**
     * Need to also check ports
     */
    public function testIsSSLD() {
        // If it's HTTPS is set, SSL
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'HTTPS';

        $this->assertTrue( security::is_ssl() );
    }

    /**
     * Need to also check ports
     */
    public function testIsSSLE() {
        // If it's HTTPS is not set, no SSL
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'HTTP';

        $this->assertFalse( security::is_ssl() );
    }

    /**
     * Check the base encrypt
     */
    public function testEncryptA() {
        // Initialize variables
        $string = 'Hello World!';

        // Encrypt string
        $encrypted_string = security::encrypt( $string, 'random-hash' );

        // Make sure it's not there
        $this->assertFalse( stristr( $encrypted_string, $string ) );
    }

    /**
     * Check to see if base64_encoding works
     *
     * @depends testEncryptA
     */
    public function testEncryptB() {
        // Initialize variables
        $string = 'Hello World!';

        // Encrypt string in two different ways
        $encrypted_string = security::encrypt( $string, 'random-hash' );
        $base64_encoded_encrypted_string = security::encrypt( $string, 'random-hash', true );

        $this->assertEquals( base64_encode( $encrypted_string ), $base64_encoded_encrypted_string );
    }

    /**
     * Decrypt a string
     *
     * @depends testEncryptA
     */
    public function testDecryptA() {
        // Initialize variables
        $string = 'Hello World!';

        // Encrypt string in two different ways
        $encrypted_string = security::encrypt( $string, 'random-hash' );

        // Decrypt string
        $decrypted_string = security::decrypt( $encrypted_string, 'random-hash' );

        $this->assertEquals( $string, $decrypted_string );
    }

    /**
     * Decrypt a string
     *
     * @depends testEncryptB
     * @depends testDecryptA
     */
    public function testDecryptB() {
        // Initialize variables
        $string = 'Hello World!';

        // Encrypt string in two different ways
        $encrypted_string = security::encrypt( $string, 'random-hash', true );

        // Decrypt string
        $decrypted_string = security::decrypt( $encrypted_string, 'random-hash', '', true );

        $this->assertEquals( $string, $decrypted_string );
    }

    /**
     * Test Creating a salt
     */
    public function testSalt() {
        // Initialize variables
        $string = 'Hello World!';

        // Create salt
        $salt = security::salt( $string );

        $this->assertGreaterThan( 32, strlen( $salt ) );
    }

    /**
     * Test Creating a hash
     *
     * @depends testSalt
     */
    public function testHash() {
        // Initialize variables
        $string = 'Hello World!';

        // Create salt
        $salt = security::hash( $string, 'test-method!' );

        // MD5 comes back with 32 characters if it worked
        $this->assertEquals( 32, strlen( $salt ) );
    }

    /**
     * Test Generating a random password
     */
    public function testGeneratePassword() {
        // Asking for a password 15 characters long
        $password = security::generate_password( 15 );

        // Make sure it's 15 characters
        $this->assertEquals( 15, strlen( $password ) );
    }

    /**
     * Test Encrypting an email
     */
    public function testEncryptEmailA() {
        // Encrypted email
        $encrypted_email = security::encrypt_email( 'john@doe.com', '', false );

        // Proper email =
        // Make sure it's 15 characters
        $this->assertEquals( 'john&#64;doe&#46;&#99;&#111;&#109;', $encrypted_email );
    }

    /**
     * Test Encyprting an email into an anchor
     *
     * @depends testEncryptEmailA
     */
    public function testEncryptEmailB() {
        // Encrypted anchor
        $encrypted_anchor = security::encrypt_email( 'john@doe.com', 'John Doe' );

        // Proper email =
        // Make sure it's 15 characters
        $this->assertEquals( "<a href='&#109;&#097;&#105;&#108;&#116;&#111;&#058;john&#64;doe&#46;&#99;&#111;&#109;' title='John Doe'>john&#64;doe&#46;&#99;&#111;&#109;</a>", $encrypted_anchor );
    }
}