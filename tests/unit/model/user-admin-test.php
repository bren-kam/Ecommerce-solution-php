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
     * Get getting a valid email
     */
    public function testValidGetByEmail() {
         // Account - Role 5
        $valid_email = 'test@studio98.com';

        $this->user->get_by_email( $valid_email );

        $this->assertNotNull( $this->user->id );
    }

    /**
     * Get getting an invalid email
     */
    public function testInvalidGetByEmail() {
         // Account - Role 5
        $valid_email = 'testasd8';

        $this->user->get_by_email( $valid_email );

        $this->assertNull( $this->user->id );
    }

    /**
     * Test a valid has_permission
     */
    public function testValidHasPermission() {
        $this->user->role = 5;

        $this->assertFalse( $this->user->has_permission( 7 ) );
    }

    /**
     * Test and invalid has_permission
     */
    public function testInvalidHasPermission() {
        $this->user->role = 5;

        $this->assertTrue( $this->user->has_permission( 5 ) );
    }

    /**
     * Test and valid record login
     */
    public function testValidRecordLogin() {
        // Setup ID
        $this->user->id = 514;

        // Record login
        $this->user->record_login();

        $this->assertTrue( false != stristr( $this->user->get_last_query(), 'UPDATE `users` SET `last_login`' ) );
        $this->assertEquals( $this->user->get_row_count(), 1 );
    }

    /**
     * Test and invalid record login
     */
    public function testInvalidRecordLogin() {
        // Record login
        $this->user->record_login();

        $this->assertFalse( stristr( $this->user->get_last_query(), 'UPDATE `users` SET `last_login`' ) );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->user = null;
    }
}
