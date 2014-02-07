<?php

require_once 'test/base-database-test.php';

class UserTest extends BaseDatabaseTest {
    const COMPANY_ID = 7;
    const EMAIL = 'test@greysuitretail.com';
    const CONTACT_NAME = 'Joe Smith';

    // Products
    const PUBLISH_DATE = '2014-01-01 00:00:00';

    /**
     * @var User
     */
    private $user;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->user = new User();

        // Define
        $this->phactory->define( 'users', array( 'company_id' => self::COMPANY_ID, 'email' => self::EMAIL, 'contact_name' => self::CONTACT_NAME, 'role' => User::ROLE_STORE_OWNER, 'status' => User::STATUS_ACTIVE ) );
        $this->phactory->define( 'products', array( 'publish_date' => self::PUBLISH_DATE ) );
        $this->phactory->recall();
    }


    /**
     * Test Getting a user
     */
    public function testGet() {
        // Create
        $ph_user = $this->phactory->create('users');

        // Get
        $this->user->get( $ph_user->user_id );

        // Assert
        $this->assertEquals( self::EMAIL, $this->user->email );
    }

    /**
     * Test has Permission
     */
    public function testHasPermission() {
        // Assign Role
        $this->user->role = User::ROLE_STORE_OWNER;

        // Assert
        $this->assertFalse( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) );
        $this->assertTrue( $this->user->has_permission( User::ROLE_STORE_OWNER ) );
    }

    /**
     * Test Getting Users
     *
     * @depends testHasPermission
     */
    public function testGetAll() {
        // Declare
        $company_id = 2;
        $email = 'sweet@greysuitretail.com';

        // Create
        $this->phactory->create('users');
        $this->phactory->create( 'users', compact( 'company_id', 'email' ) );

        // Assign Role
        $this->user->company_id = self::COMPANY_ID;
        $this->user->role = User::ROLE_STORE_OWNER;

        // Get
        $users = $this->user->get_all();
        $user = current( $users );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertEquals( self::COMPANY_ID, $user->company_id );
        $this->assertCount( 1, $users );

        // Assign Role
        $this->user->role = User::ROLE_ADMIN;

        // Get
        $users = $this->user->get_all();

        // Assert
        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertCount( 2, $users );
    }

    /**
     * Test creating a user
     */
    public function testCreate() {
        // Create
        $this->user->email = self::EMAIL;
        $this->user->create();

        // Assert
        $this->assertNotNull( $this->user->id );

        // Get
        $ph_user = $this->phactory->get( 'users', array( 'user_id' => $this->user->id ) );

        // Assert
        $this->assertEquals( self::EMAIL, $ph_user->email );
    }

    /**
     * Test updating a user
     */
    public function testUpdate() {
        // Create
        $ph_user = $this->phactory->create('users');

        // Update test
        $this->user->id = $ph_user->user_id;
        $this->user->email = 'jiminy@cricket.com';
        $this->user->save();

        // Get
        $ph_user = $this->phactory->get( 'users', array( 'user_id' => $ph_user->user_id ) );

        // Assert
        $this->assertEquals( $this->user->email, $ph_user->email );
    }

    /**
     * Test Setting the password
     *
     * @depends testGet
     */
    public function testSetPassword() {
        // Declare
        $new_password = 'Hello world!';

        // Create
        $ph_user = $this->phactory->create('users');

        // Set
        $this->user->id = $ph_user->user_id;
        $this->user->set_password( $new_password );

        // Get
        $ph_user = $this->phactory->get( 'users', array( 'user_id' => $ph_user->user_id ) );
        $expected_password = md5( $new_password );

        // Assert
        $this->assertEquals( $expected_password, $ph_user->password );
    }

    /**
     * Tests accountlogin
     */
    public function testAccountLogin() {
        // Declare
        $password = 'blabla';
        $incorrect_password = 'bla';

        // Create
        $this->phactory->create( 'users', array( 'password' => md5($password) ) );

        // Scenario 1 - Account Login, wrong password
        $this->assertFalse( $this->user->login( self::EMAIL, $incorrect_password ) );

        // Scenario 2 - Account login, correct password
        $this->assertTrue( $this->user->login( self::EMAIL, $password ) );

        // Assert
        $this->assertEquals( self::CONTACT_NAME, $this->user->contact_name );
    }

    /**
     * Tests admin login
     */
    public function testAdminLogin() {
        // Declare
        $password = 'blabla';
        $incorrect_password = 'bla';

        // Create
        $this->phactory->create( 'users', array( 'password' => md5($password) ) );

        // Scenario 1 -- Admin login, Role too lower
        $this->assertFalse( $this->user->login( self::EMAIL, $password, true ) );

        // Reset
        $this->phactory->recall();

        // Create with admin role
        $this->phactory->create( 'users', array( 'password' => md5($password), 'role' => User::ROLE_ADMIN ) );

        // Scenario 2 -- Admin login, incorrect password
        $this->assertFalse( $this->user->login( self::EMAIL, $incorrect_password ) );

        // Scenario 3 - Admin login, correct password
        $this->assertTrue( $this->user->login( self::EMAIL, $password ) );

        // Assert
        $this->assertEquals( self::CONTACT_NAME, $this->user->contact_name );
    }


    /**
     * Get getting a valid email
     */
    public function testGetByEmailA() {
        // Create
        $this->phactory->create( 'users', array( 'status' => User::STATUS_INACTIVE ) );

        // Scenario 1 - Status required
        $this->user->get_by_email( self::EMAIL );

        // Assert
        $this->assertNull( $this->user->contact_name );

        // Scenario 2- Status not required
        $this->user->get_by_email( self::EMAIL, false );

        // Assert
        $this->assertEquals( self::CONTACT_NAME, $this->user->contact_name );
    }

    /**
     * Get getting a valid email
     */
    public function testGetByEmailB() {
        // Create
        $this->phactory->create('users');

        // Scenario 1 - Status required
        $this->user->get_by_email( self::EMAIL );

        // Assert
        $this->assertEquals( self::CONTACT_NAME, $this->user->contact_name );
    }

    /**
     * Get Admin Users
     *
     * @depends testHasPermission
     */
    public function testGetAdminUsers() {
        // Declare
        $company_id = 2;
        $email = 'sweet@greysuitretail.com';
        $role = User::ROLE_ONLINE_SPECIALIST;

        // Create
        $this->phactory->create( 'users', compact('company_id') ); // Not admin user

        // Setup
        $this->user->company_id = self::COMPANY_ID;

        // Shouldn't have anything
        $this->assertEmpty( $this->user->get_admin_users() );

        // Create a user with right role
        $this->phactory->create( 'users', compact( 'company_id', 'email', 'role' ) );

        // Get
        $this->assertEmpty( $this->user->get_admin_users() ); // Wrong company

        // Get
        $this->user->role = User::ROLE_ADMIN;
        $users = $this->user->get_admin_users();
        $user = current( $users );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertEquals( self::CONTACT_NAME, $user->contact_name );
        $this->assertCount( 1, $users );
    }


    /**
     * Test and invalid record login
     */
    public function testRecordLogin() {
        // Create
        $ph_user = $this->phactory->create('users');

        // Declare
        $datetime = new DateTime();

        // Record login
        $this->user->id = $ph_user->user_id;
        $this->user->record_login();

        // Get
        $ph_user = $this->phactory->get( 'users', array( 'user_id' => $ph_user->user_id ) );
        $last_login = new DateTime( $ph_user->last_login );

        // It should be more recent
        $this->assertLessThan( $datetime->getTimestamp(), $last_login->getTimestamp() );
    }


    /**
     * Test listing all accounts
     */
    public function testListAll() {
        // Create
        $this->phactory->create('users');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $this->user );
        $dt->order_by( '`contact_name`', '`email`', '`role`' );
        $dt->search( array( '`contact_name`' => true, '`email`' => true ) );

        // Get
        $users = $this->user->list_all( $dt->get_variables() );
        $user = current( $users );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertEquals( self::CONTACT_NAME, $user->contact_name );

        // Get rid of everything
        unset( $_GET, $dt, $users);
    }

    /**
     * Test counting the accounts
     */
    public function testCountAll() {
        // Create
        $this->phactory->create('users');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $this->user );
        $dt->order_by( '`contact_name`', '`email`', '`role`' );
        $dt->search( array( '`contact_name`' => true, '`email`' => true ) );

        // Get
        $count = $this->user->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Test Get Product Users
     *
     * @depends testHasPermission
     */
    public function testGetProductUsers() {
        // Declare
        $company_id = 2;
        $email = 'sweet@gresuitretail.com';

        // Create User
        $ph_user = $this->phactory->create( 'users', compact('company_id') );
        $ph_user_2 = $this->phactory->create( 'users', compact('email') );
        $this->phactory->create( 'products', array( 'user_id_created' => $ph_user->user_id ) );
        $this->phactory->create( 'products', array( 'user_id_created' => $ph_user_2->user_id ) );

        // Assign Role
        $this->user->company_id = $company_id;
        $this->user->role = User::ROLE_STORE_OWNER;

        // Get - Can only get the same company
        $users = $this->user->get_product_users();
        $user = current( $users );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertCount( 1, $users );
        $this->assertEquals( $ph_user->user_id, $user->user_id );

        // Change role
        $this->user->role = User::ROLE_ADMIN;

        // Get -- should get them all
        $users = $this->user->get_product_users();

        // Assert
        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertCount( 2, $users );
    }

    /**
     * Test Autocomplete
     *
     * @depends testHasPermission
     */
    public function testAutocomplete() {
        // Declare
        $query = substr( self::CONTACT_NAME, 0, 3 );
        $field = 'contact_name';
        $expected_users = array( array( $field => self::CONTACT_NAME ) );
        $company_id = 2;

        // Create
        $this->phactory->create('users');

        // Get Users - no role and no company should be empty
        $users = $this->user->autocomplete( $query, $field );

        // Assert
        $this->assertEmpty( $users );

        // Assign
        $this->user->company_id = self::COMPANY_ID;

        // Should get a user now
        $users = $this->user->autocomplete( $query, $field );

        // Assert
        $this->assertEquals( $expected_users, $users );

        // Changing things
        $this->user->company_id = $company_id; // Different company
        $this->user->role = User::ROLE_ADMIN;

        // Should get a user now
        $users = $this->user->autocomplete( $query, $field );

        // Assert
        $this->assertEquals( $expected_users, $users );
    }


    /**
     * Test Get Role Name
     */
    public function testGetRoleName() {
        // Declare
        $role = User::ROLE_MARKETING_SPECIALIST;
        $name = 'Marketing Specialist';

        // Get
        $fetched_name = $this->user->get_role_name( $role );

        // Assert
        $this->assertEquals( $name, $fetched_name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->user = null;
    }
}
