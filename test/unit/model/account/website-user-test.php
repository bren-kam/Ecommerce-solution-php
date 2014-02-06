<?php

require_once 'test/base-database-test.php';

class WebsiteUserTest extends BaseDatabaseTest {
    const EMAIL = 'test@greysuitretail.com';
    const BILLING_FIRST_NAME = 'Tommy Dickinson';

    /**
     * @var WebsiteUser
     */
    private $website_user;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_user = new WebsiteUser();

        // Define
        $this->phactory->define( 'website_users', array( 'website_id' => self::WEBSITE_ID, 'email' => self::EMAIL, 'billing_first_name' => self::BILLING_FIRST_NAME ) );
        $this->phactory->recall();
    }


    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_user = $this->phactory->create('website_users');

        // Get
        $this->website_user->get( $ph_website_user->website_user_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::EMAIL, $this->website_user->email );
    }

    /**
     * Test Get
     */
    public function testGetByEmail() {
        // Create
        $this->phactory->create('website_users');

        // Get
        $this->website_user->get_by_email( self::EMAIL, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::BILLING_FIRST_NAME, $this->website_user->billing_first_name );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_website_user = $this->phactory->create('website_users');

        // Get
        $this->website_user->id = $ph_website_user->website_user_id;
        $this->website_user->email = 'sweet@greysuitretail.com';
        $this->website_user->save();

        // Get
        $ph_website_user = $this->phactory->get( 'website_users', array( 'website_user_id' => $ph_website_user->website_user_id ) );

        // Assert
        $this->assertEquals( $this->website_user->email, $ph_website_user->email );
    }

    /**
     * Set Password
     */
    public function testSetPassword() {
        // Declare
        $password = '12345';
        $expected_password = md5( $password );

        // Create
        $ph_website_user = $this->phactory->create('website_users');

        // Get
        $this->website_user->id = $ph_website_user->website_user_id;
        $this->website_user->set_password( $password );

        // Get
        $ph_website_user = $this->phactory->get( 'website_users', array( 'website_user_id' => $ph_website_user->website_user_id ) );

        // Assert
        $this->assertEquals( $expected_password, $ph_website_user->password );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Create
        $ph_website_user = $this->phactory->create('website_users');

        // Delete
        $this->website_user->id = $ph_website_user->website_user_id;
        $this->website_user->remove();

        // Get
        $ph_website_user = $this->phactory->get( 'website_users', array( 'website_user_id' => $ph_website_user->website_user_id ) );

        // Assert
        $this->assertNull( $ph_website_user );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_users');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`email`', '`billing_first_name`', '`date_registered`' );

        // Get
        $website_users = $this->website_user->list_all( $dt->get_variables() );
        $website_user = current( $website_users );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteUser', $website_users );
        $this->assertEquals( self::EMAIL, $website_user->email );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_users');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`email`', '`billing_first_name`', '`date_registered`' );

        // Get
        $count = $this->website_user->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_user = null;
    }
}
