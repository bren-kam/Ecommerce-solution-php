<?php

require 'base-database-test.php';

class UserTest extends BaseDatabaseTest {
    /**
     * @var User
     */
    private $user;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->user = new User();
    }

    public function testInvalidLogin() {
        // Make sure it returns false
        $result = $this->user->login( 'blabla', 'bla' );

        $this->assertFalse( $result );
    }

    public function testValidLoginRole5() {
        // Account - Role 5
        $valid_email = 'test@studio98.com';
        $valid_pass = 'pass123';

        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNotNull( $this->user->id );
        $this->assertTrue( $result );
    }

    public function testValidLoginOnAdmin() {
        // Account - Role 5
        $valid_email = 'test@studio98.com';
        $valid_pass = 'pass123';

        // Let's try again if we're on the admin
        define( 'ADMIN', 1 );

        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNull( $this->user->id );
        $this->assertFalse( $result );
    }

    public function testValidLoginOnAdminRole7() {
        define( 'ADMIN', 1 );
        // Admin Role - 7
        $valid_email = 'test@greysuitretail.com';
        $valid_pass = 'sapp123';

        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNotNull( $this->user->id );
        $this->assertTrue( $result );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        define( 'ADMIN', 0 );
        $this->user = null;
    }
}
