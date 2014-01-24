<?php

require_once 'test/base-database-test.php';

class AuthUserWebsiteTest extends BaseDatabaseTest {
    const USER_ID = 1;
    const WEBSITE_ID = 3;
    const PAGES = 1;

    // Users
    const CONTACT_NAME = 'Billy Young';
    const EMAIL = 'billy@young.com';

    /**
     * @var AuthUserWebsite
     */
    private $auth_user_website;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->auth_user_website = new AuthUserWebsite();

        // Define
        $this->phactory->define( 'auth_user_websites', array( 'website_id' => self::WEBSITE_ID, 'user_id' => self::USER_ID, 'pages' => self::PAGES ) );
        $this->phactory->define( 'users', array( 'email' => self::EMAIL, 'contact_name' => self::CONTACT_NAME, 'status' => User::STATUS_ACTIVE ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Getting by account
     */
    public function testGetByAccount() {
        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create( 'auth_user_websites', array( 'user_id' => $ph_user->user_id ) );

        // Get
        $users = $this->auth_user_website->get_by_account( self::WEBSITE_ID );
        $user = current( $users );

        $this->assertContainsOnlyInstancesOf( 'User', $users );
        $this->assertEquals( self::EMAIL, $user->email );
    }

    /**
     * Test Getting the Default
     */
    public function testGet() {
        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create( 'auth_user_websites', array( 'user_id' => $ph_user->user_id ) );

        // Get
        $this->auth_user_website->get( $ph_user->user_id, self::WEBSITE_ID );

        $this->assertEquals( self::EMAIL, $this->auth_user_website->email );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->auth_user_website->website_id = self::WEBSITE_ID;
        $this->auth_user_website->user_id = self::USER_ID;
        $this->auth_user_website->create();

        $this->assertNotNull( $this->auth_user_website->id );

        // Make sure it's in the database
        $ph_auth_user_website = $this->phactory->get( 'auth_user_websites', array( 'website_id' => self::WEBSITE_ID ) );

        $this->assertEquals( self::USER_ID, $ph_auth_user_website->user_id );
    }

    /**
     * Test save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $ph_auth_user_website = $this->phactory->create( 'auth_user_websites' );

        // Save
        $this->auth_user_website->website_id = $ph_auth_user_website->website_id;
        $this->auth_user_website->user_id = $ph_auth_user_website->user_id;
        $this->auth_user_website->pages = 0;
        $this->auth_user_website->save();

        // Make sure it's in the database
        $ph_auth_user_website = $this->phactory->get( 'auth_user_websites', array( 'website_id' => self::WEBSITE_ID, 'user_id' => self::USER_ID ) );

        $this->assertEquals( $this->auth_user_website->pages, $ph_auth_user_website->pages );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $ph_auth_user_website = $this->phactory->create( 'auth_user_websites' );

        // Remove
        $this->auth_user_website->website_id = $ph_auth_user_website->website_id;
        $this->auth_user_website->user_id = $ph_auth_user_website->user_id;
        $this->auth_user_website->remove();

        // Make sure it's gone
        $ph_auth_user_website = $this->phactory->get( 'auth_user_websites', array( 'website_id' => self::WEBSITE_ID, 'user_id' => self::USER_ID ) );

        $this->assertNull( $ph_auth_user_website );
    }

    /**
     * Test Is Authorized
     */
    public function testIsAuthorized() {
        // Create
        $this->phactory->create( 'auth_user_websites' );

        // Get
        $user_id = $this->auth_user_website->is_authorized( self::USER_ID, self::WEBSITE_ID);

        $this->assertEquals( self::USER_ID, $user_id );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub User
        $stub_user = $this->getMock('User');

        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create( 'auth_user_websites', array( 'user_id' => $ph_user->user_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'u.`email`', 'auw.`pages`', 'auw.`products`', 'auw.`analytics`', 'auw.`blog`', 'auw.`email_marketing`', 'auw.`shopping_cart`' );

        $auth_user_websites = $this->auth_user_website->list_all( $dt->get_variables() );
        $auth_user_website = current( $auth_user_websites );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'AuthUserWebsite', $auth_user_websites );
        $this->assertEquals( self::EMAIL, $auth_user_website->email );

        // Get rid of everything
        unset( $user, $_GET, $dt, $auth_users );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Stub User
        $stub_user = $this->getMock('User');

        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create( 'auth_user_websites', array( 'user_id' => $ph_user->user_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'u.`email`', 'auw.`pages`', 'auw.`products`', 'auw.`analytics`', 'auw.`blog`', 'auw.`email_marketing`', 'auw.`shopping_cart`' );

        $count = $this->auth_user_website->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->auth_user_website = null;
    }
}
