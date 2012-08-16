<?php

require_once 'base-database-test.php';

class UserAccountTest extends BaseDatabaseTest {
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
     * Test Getting a user
     */
    public function testGet() {
        $user_id = 513;
        $email = 'test@studio98.com';

        $this->user->get( $user_id );

        $this->assertEquals( $this->user->email, $email );
    }

    /**
     * Test Getting Users
     */
    public function testGetAll() {
        $this->user->role = 5;
        $this->user->company_id = 1;

        $users = $this->user->get_all();

        // Make sure that it returned users
        $this->assertTrue( $users[0] instanceof User );

        $same_company = true;

        // Make sure that they're only the same company
        foreach ( $users as $user ) {
            if ( $user->company_id != $this->user->company_id && $user->id != 493 ) {
                $same_company = false;
                break;
            }
        }

        $this->assertTrue( $same_company );
    }

    /**
     * Test creating a user
     *
     * @depends testGet
     */
    public function testCreate() {
        $new_email = 'test' . rand( 0, 10000 ) . '@phpunit-test.com';
        $this->user->email = $new_email;
        $this->user->create();

        $this->assertTrue( !is_null( $this->user->id ) );

        // Make sure it's in the database
        $this->user->get( $this->user->id );

        $this->assertEquals( $new_email, $this->user->email );

        // Delete the user
        $this->db->delete( 'users', array( 'user_id' => $this->user->id ), 'i' );
    }

    /**
     * Test updating a user
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $new_email = 'test' . rand( 0, 10000 ) . '@phpunit-test.com';
        $this->user->email = $new_email;
        $this->user->create();

        // Update test
        $this->user->contact_name = 'Jiminy Cricket';
        $this->user->email = 'jiminy@cricket.com';
        $this->user->update();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->user->id ) );

        // Now check it!
        $this->user->get( $this->user->id );

        $this->assertEquals( 'jiminy@cricket.com', $this->user->email );

        // Delete the company
        $this->db->delete( 'users', array( 'user_id' => $this->user->id ), 'i' );
    }

    /**
     * Test Setting the password
     *
     * @depends testGet
     */
    public function testSetPassword() {
        $new_password = 'Hello world!';

        $this->user->get(513);
        $this->user->set_password( $new_password );

        $password = $this->db->get_var( 'SELECT `password` FROM `users` WHERE `user_id` = 513' );

        $this->assertEquals( md5($new_password), $password );

        $this->db->update( 'users', array( 'password' => md5('pass123') ), array( 'user_id' => 513 ), 's', 'i' );
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
     * Tests a valid login on the account side
     */
    public function testValidLoginOnAccountRole5() {
        // Account - Role 5
        $valid_email = 'test@studio98.com';
        $valid_pass = 'pass123';

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
     * Get Admin Users
     */
    public function testGetAdminUsersWithRole5() {
        $this->assertFalse( $this->user->get_admin_users() );
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
     * Test and invalid record login
     */
    public function testInvalidRecordLogin() {
        // Setup variables
        $datetime = new DateTime();

        // Record login
        $this->user->record_login();

        // Get the last login as a date time
        $last_login = new DateTime( $this->db->get_var( 'SELECT `last_login` FROM `users` WHERE `user_id` = 513' ) );

        // It should be more recent
        $this->assertLessThan( $datetime->getTimestamp() - 5, $last_login->getTimestamp() );
    }

    /**
     * Test and valid record login
     */
    public function testValidRecordLogin() {
        // Setup variables
        $this->user->id = 513;
        $datetime = new DateTime();

        // Record login
        $this->user->record_login();

        // Get the last login as a date time
        $last_login = new DateTime( $this->db->get_var( 'SELECT `last_login` FROM `users` WHERE `user_id` = ' . (int) $this->user->id ) );

        // It should be more recent
        $this->assertGreaterThan( $datetime->getTimestamp() - 5, $last_login->getTimestamp() );
    }

    /**
     * Test listing all accounts
     */
    public function testListAll() {
        $this->user->get_by_email('test@studio98.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $this->user );
        $dt->order_by( 'a.`contact_name`', 'a.`email`', 'phone', 'b.`domain`', 'a.`role`' );
        $dt->search( array( 'a.`contact_name`' => true, 'a.`email`' => true, 'b.`domain`' => true ) );

        $users = $this->user->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( is_array( $users ) );

        // Joe Schmoe is a user with ID 496
        $joe_schmoe_exists = false;

        if ( is_array( $users ) )
        foreach ( $users as $user ) {
            if ( 496 == $user->id ) {
                $joe_schmoe_exists = true;
                break;
            }
        }

        // Make sure they exist
        $this->assertTrue( $joe_schmoe_exists );

        // Get rid of everything
        unset( $user, $_GET, $dt, $users, $account, $joe_schmoe_exists );
    }

    /**
     * Test counting the accounts
     */
    public function testCountAll() {
        $this->user->get_by_email('test@studio98.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $this->user );
        $dt->order_by( 'a.`contact_name`', 'a.`email`', 'phone', 'b.`domain`', 'a.`role`' );
        $dt->search( array( 'a.`contact_name`' => true, 'a.`email`' => true, 'b.`domain`' => true ) );

        $count = $this->user->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->user = null;
    }
}
