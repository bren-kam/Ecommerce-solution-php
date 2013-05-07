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
     * Test Getting a user
     */
    public function testGet() {
        // Setup Variables
        $user_id = -51;
        $email = 'test@greysu1tretail.com';

        // Create User
        $this->db->insert( 'users', compact( 'user_id', 'email' ), 'is' );

        // Get User
        $this->user->get( $user_id );

        $this->assertEquals( $this->user->email, $email );

        // Delete
        $this->db->delete( 'users', compact( 'user_id' ), 'i' );
    }

    /**
     * Test Getting Users
     */
    public function testGetAll() {
        $this->user->role = 8;
        $this->user->company_id = 1;

        $users = $this->user->get_all();

        // Make sure that it returned users
        $this->assertTrue( $users[0] instanceof User );

        $same_company = false;

        // Make sure that they're only the same company
        foreach ( $users as $user ) {
            if ( $user->company_id != $this->user->company_id ){
                $same_company = true;
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
        $this->user->save();

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

        $this->user->get(514);
        $this->user->set_password( $new_password );

        $password = $this->db->get_var( 'SELECT `password` FROM `users` WHERE `user_id` = 514' );

        $this->assertEquals( md5($new_password), $password );

        $this->db->update( 'users', array( 'password' => md5('sapp123') ), array( 'user_id' => 514 ), 's', 'i' );
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
        $email = 'test@greysu1tretail.com';
        $straight_password = 'sapp123';
        $password = md5( $straight_password );
        $user_id = -37;
        $role = 7;

        // Create User
        $this->db->insert( 'users', compact( 'user_id', 'email', 'password', 'role' ), 'issi' );

        $result = $this->user->login( $email, 'sapp123', $straight_password );

        $this->assertNotNull( $this->user->id );
        $this->assertTrue( $result );

        // Delete User
        $this->db->delete( 'users', compact( 'user_id' ), 'i' );
    }

    /**
     * Get getting a valid email
     */
    public function testValidGetByEmail() {
        // Account - Role 5
        $email = 'test@ground-testing.com';
        $user_id = -33;
        $role = 5;
        $status = 1;

        // Create User
        $this->db->insert( 'users', compact( 'user_id', 'email', 'role', 'status' ), 'isii' );

        $this->user->get_by_email( $email );

        $this->assertNotNull( $this->user->id );

        // Delete User
        $this->db->delete( 'users', compact( 'user_id' ), 'i' );
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
     * Get Admin Users with a role of 5
     *
     * @depends testGet
     */
    public function testGetAdminUsersWithRole5() {
        $this->user->role = 5;
        $this->user->company_id = 1;

        $users =  $this->user->get_admin_users();

        $this->assertTrue( $users[0] instanceof User );

        $user = new User;
        $user->get( $users[0]->user_id );

        $this->assertEquals( $user->company_id, 1 );
    }

    /**
     * Get Admin Users when you have a role of 8
     *
     * @depends testGet
     */
    public function testGetAdminUsersWithRole8() {
        $this->user->role = 8;

        $users =  $this->user->get_admin_users();

        $this->assertTrue( $users[0] instanceof User );
    }

    /**
     * Test a valid has_permission
     */
    public function testValidHasPermission() {
        $this->user->role = 5;

        $this->assertFalse( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) );
    }

    /**
     * Test and invalid has_permission
     */
    public function testInvalidHasPermission() {
        $this->user->role = 5;

        $this->assertTrue( $this->user->has_permission( User::ROLE_STORE_OWNER ) );
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
        $last_login = new DateTime( $this->db->get_var( 'SELECT `last_login` FROM `users` WHERE `user_id` = 514' ) );

        // It should be more recent
        $this->assertLessThan( $datetime->getTimestamp() - 5, $last_login->getTimestamp() );
    }

    /**
     * Test and valid record login
     */
    public function testValidRecordLogin() {
        // Setup variables
        $this->user->id = 514;
        $datetime = new DateTime();

        // Record login
        $this->user->record_login();

        // Get the last login as a date time
        $last_login = new DateTime( $this->db->get_var( 'SELECT `last_login` FROM `users` WHERE `user_id` = ' . (int) $this->user->id ) );

        // It should be more recent
        $this->assertGreaterThan( $datetime->getTimestamp() - 300, $last_login->getTimestamp() );
    }

    /**
     * Test listing all accounts
     */
    public function testListAll() {
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


        // Make sure they exist
        $this->assertTrue( current( $users ) instanceof User );

        // Get rid of everything
        unset( $_GET, $dt, $users );
    }

    /**
     * Test counting the accounts
     */
    public function testCountAll() {
        $this->user->get_by_email('test@greysu1tretail.com');

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
     * Test Get Product Users
     */
    public function testGetProductUsersA() {
        $this->user->role = 8;
        $users = $this->user->get_product_users();
        $user_id = (int) $users[0]->id;

        // Get the product that he created
        $product_id = $this->db->get_var( "SELECT `product_id` FROM `products` WHERE `publish_date` <> '0000-00-00 00:00:00' AND ( `user_id_modified` = $user_id OR `user_id_created` = $user_id ) LIMIT 1" );

        $this->assertGreaterThan( 0, $product_id );
    }

    /**
     * Test Get Product Users
     */
    public function testGetProductUsersB() {
        $this->user->role = 7;
        $this->user->company_id = 1;

        // Get Product Users
        $users = $this->user->get_product_users();
        $user_id = (int) $users[0]->id;

        // Make sure they have the same company id
        $company_id = $this->db->get_var( "SELECT `company_id` FROM `users` WHERE `user_id` = $user_id" );

        // Should be the same
        $this->assertEquals( $this->user->company_id, $company_id );

        // Get the product that he created
        $product_id = $this->db->get_var( "SELECT `product_id` FROM `products` WHERE `publish_date` <> '0000-00-00 00:00:00' AND ( `user_id_modified` = $user_id OR `user_id_created` = $user_id ) LIMIT 1" );

        // Make sure it could grab it
        $this->assertGreaterThan( 0, $product_id );
    }

    /**
     * Test Autocomplete
     */
    public function testAutocompleteA() {
        // Declare variables
        $autocomplete_name = 'Kerry Lebensburger';

        // Assign Role
        $this->user->role = 8;

        // Get Users
        $users = $this->user->autocomplete( 'Kerry', 'contact_name' );

        $this->assertEquals( $users[0]['contact_name'], $autocomplete_name );
    }

    /**
     * Test Autocomplete with a lower Role
     */
    public function testAutocompleteB() {
        // Assign Role
        $this->user->role = 7;
        $this->user->company_id = 4;

        // Create user
        $this->db->insert( 'users', array( 'company_id' => 4, 'email' => md5(time()), 'password' => md5(microtime()), 'contact_name' => 'Habba dashery', 'role' => 8 ), 'isssi' );

        $user_id = $this->db->get_insert_id();

        // Get Users
        $users = $this->user->autocomplete( 'Habba', 'contact_name' );

        $this->assertEquals( $users[0]['contact_name'], 'Habba dashery' );

        // Delete
        $this->db->delete( 'users', array( 'user_id' => $user_id ), 'i' );
    }

    /**
     * Test Autocomplete with a lower Role and wrong company
     */
    public function testAutocompleteC() {
        // Assign Role
        $this->user->role = 7;
        $this->user->company_id = 1;

        // Create user
        $this->db->insert( 'users', array( 'company_id' => 3, 'email' => md5(time()), 'password' => md5(microtime()), 'contact_name' => 'Habba dashery', 'role' => 8 ), 'isssi' );

        $user_id = $this->db->get_insert_id();

        // Get Users
        $users = $this->user->autocomplete( 'Habba', 'contact_name' );


        $this->assertFalse( isset( $users[0] ) );

        // Delete
        $this->db->delete( 'users', array( 'user_id' => $user_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->user = null;
    }
}
