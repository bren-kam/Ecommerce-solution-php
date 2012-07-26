<?php

if ( !defined('MODEL_PATH') )
    define( 'MODEL_PATH', '' );

require_once 'base-database-test.php';

class UserAdminTest extends BaseDatabaseTest {
    /**
     * @var User
     */
    private $user;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->user = new User( 1 );
    }

    /**
     * Tests invalid login
     */
    public function testInvalidLogin() {
        // Make sure it returns false
        $result = $this->user->login( 'blabla', 'bla' );

        $this->assertFalse( $result );
    }

    /**
     * Tests invalid login on admin side
     */
    public function testInvalidLoginOnAdminRole5() {
        // Account - Role 5
        $valid_email = 'test@studio98.com';
        $valid_pass = 'pass123';

        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNull( $this->user->id );
        $this->assertFalse( $result );
    }

    /**
     * Tests a valid login on admin side
     */
    public function testValidLoginOnAdminRole7() {
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
