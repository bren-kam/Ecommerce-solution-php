<?php

require_once 'base-database-test.php';

class AuthUserWebsiteTest extends BaseDatabaseTest {
    /**
     * @var AuthUserWebsite
     */
    private $auth_user_website;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->auth_user_website = new AuthUserWebsite();
    }
    
    /**
     * Test Getting by account
     */
    public function testGetByAccount() {
        // Declare variables
        $website_id = -5;
        $contact_name = 'Enjoy us';
        $email = md5(microtime()) . '@cash.com';
        $status = 1;

        // Insert
        $user_id = $this->db->insert( 'users', compact( 'email', 'contact_name', 'status' ), 'ssi' );
        $this->db->insert( 'auth_user_websites', compact( 'website_id', 'user_id' ), 'ii' );

        $users = $this->auth_user_website->get_by_account( $website_id );

        $this->assertTrue( current( $users ) instanceof User );

        // Delete
        $this->db->delete( 'auth_user_websites', compact( 'website_id' ), 'i' );
        $this->db->delete( 'users', compact( 'user_id' ), 'i' );
    }
    
    /**
     * Test Getting the Default
     */
    public function testGet() {
        // Declare variables
        $website_id = -5;
        $user_id = -7;
        $pages = -11;

        // Insert
        $this->db->insert( 'auth_user_websites', compact( 'website_id', 'user_id', 'pages' ), 'iii' );

        // Get
        $this->auth_user_website->get( $user_id, $website_id );

        $this->assertEquals( $pages, $this->auth_user_website->pages );

        // Delete
        $this->db->delete( 'auth_user_websites', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $website_id = -3;
        $user_id = -5;

        // Create
        $this->auth_user_website->website_id = $website_id;
        $this->auth_user_website->user_id = $user_id;
        $this->auth_user_website->create();

        // Make sure it's in the database
        $fetched_user_id = $this->db->get_var( 'SELECT `user_id` FROM `auth_user_websites` WHERE `auth_user_website_id` = ' . (int) $this->auth_user_website->id );

        $this->assertEquals( $user_id, $fetched_user_id );

        // Delete
        $this->db->delete( 'auth_user_websites', compact( 'website_id' ), 'i' );
    }

    /**
     * Test save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $website_id = -3;
        $user_id = -7;
        $pages = -5;

        // Create
        $this->auth_user_website->website_id = $website_id;
        $this->auth_user_website->user_id = $user_id;
        $this->auth_user_website->create();

        // Save
        $this->auth_user_website->pages = $pages;
        $this->auth_user_website->save();

        // Make sure it's in the database
        $fetched_pages = $this->db->get_var( 'SELECT `pages` FROM `auth_user_websites` WHERE `auth_user_website_id` = ' . (int) $this->auth_user_website->id );

        $this->assertEquals( $pages, $fetched_pages );

        // Delete
        $this->db->delete( 'auth_user_websites', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Declare variables
        $website_id = -5;
        $user_id = -7;

        // Insert
        $auth_user_website_id = $this->db->insert( 'auth_user_websites', compact( 'website_id', 'user_id' ), 'ii' );

        // Get
        $this->auth_user_website->get( $user_id, $website_id );

        // Remove
        $this->auth_user_website->remove();

        $auth_user_website = $this->db->get_row( 'SELECT * FROM `auth_user_websites` WHERE `auth_user_website_id` = ' . (int) $auth_user_website_id );

        // Make sure we grabbed the right one
        $this->assertFalse( $auth_user_website );
    }

    /**
     * Test Is Authorized
     */
    public function testIsAuthorized() {
        // Declare variables
        $website_id = -5;
        $user_id = -7;

        // Insert
        $this->db->insert( 'auth_user_websites', compact( 'website_id', 'user_id' ), 'ii' );

        // Get
        $fetched_authorize_user_id = $this->auth_user_website->is_authorized( $user_id, $website_id );

        $this->assertEquals( $fetched_authorize_user_id, $user_id );

        // Delete
        $this->db->delete( 'auth_user_websites',compact( 'website_id' ), 'i' );
    }

    /**
     * List All
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'u.`email`', 'auw.`pages`', 'auw.`products`', 'auw.`analytics`', 'auw.`blog`', 'auw.`email_marketing`', 'auw.`shopping_cart`' );

        $auth_users = $this->auth_user_website->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $auth_users ) instanceof AuthUserWebsite );

        // Get rid of everything
        unset( $user, $_GET, $dt, $auth_users );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
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
