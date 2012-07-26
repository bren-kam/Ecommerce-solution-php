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

    /**
     * Make sure the login function works
     */
    public function testLogin() {
        // Make sure it returns false
        $result = $this->user->login( 'blabla', 'bla' );

        $this->assertFalse( $result );

        // Account - Role 5
        $valid_email = 'test@studio98.com';
        $valid_pass = 'pass123';

        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNotNull( $this->user->id );
        $this->assertTrue( $result );

        // Let's try again if we're on the admin
        define( 'ADMIN', 1 );

        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNull( $this->user->id );
        $this->assertFalse( $result );

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
        $this->user = null;
    }
}
