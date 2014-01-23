<?php

require_once 'test/base-database-test.php';

class WebsiteUserTest extends BaseDatabaseTest {
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
    }

    /**
     * Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $email = 'cranky@crank.com';

        // Create
        $website_user_id = $this->phactory->insert( 'website_users', compact( 'website_id', 'email' ), 'is' );

        // Get
        $this->website_user->get( $website_user_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $email, $this->website_user->email );

        // Clean up
        $this->phactory->delete( 'website_users', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Get
     */
    public function testGetByEmail() {
        // Set variables
        $website_id = -7;
        $email = 'cranky@crank.com';

        // Create
        $this->phactory->insert( 'website_users', compact( 'website_id', 'email' ), 'is' );

        // Get
        $this->website_user->get_by_email( $email, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $email, $this->website_user->email );

        // Clean up
        $this->phactory->delete( 'website_users', compact( 'website_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Set variables
        $website_id = -7;
        $email = 'stranger@strange.com';

        // Create
        $website_user_id = $this->phactory->insert( 'website_users', compact( 'website_id' ), 'i' );

        // Get
        $this->website_user->get( $website_user_id, $website_id );
        $this->website_user->email = $email;
        $this->website_user->save();

        // Now check it!
        $retrieved_email = $this->phactory->get_var( 'SELECT `email` FROM `website_users` WHERE `website_user_id` = ' . (int) $website_user_id );

        $this->assertEquals( $retrieved_email, $email );

        // Clean up
        $this->phactory->delete( 'website_users', compact( 'website_id' ), 'i' );
    }

    /**
     * Set Password
     *
     * @depends testGet
     */
    public function testSetPassword() {
        // Set variables
        $website_id = -7;
        $password = '12345';
        $md5_password = md5( $password );

        // Create
        $website_user_id = $this->phactory->insert( 'website_users', compact( 'website_id' ), 'i' );

        // Get
        $this->website_user->get( $website_user_id, $website_id );
        $this->website_user->set_password( $password );

        // Now check it!
        $retrieved_password = $this->phactory->get_var( 'SELECT `password` FROM `website_users` WHERE `website_user_id` = ' . (int) $website_user_id );

        $this->assertEquals( $retrieved_password, $md5_password );

        // Clean up
        $this->phactory->delete( 'website_users', compact( 'website_id' ), 'i' );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Set variables
        $website_id = -7;
        $email = 'stranger@strange.com';

        // Create
        $website_user_id = $this->phactory->insert( 'website_users', compact( 'website_id', 'email' ), 'is' );

        // Get
        $this->website_user->get( $website_user_id, $website_id );

        // Remove/Delete
        $this->website_user->remove();

        $retrieved_email = $this->phactory->get_var( 'SELECT `email` FROM `website_users` WHERE `website_user_id` = ' . (int) $website_user_id );

        $this->assertFalse( $retrieved_email );
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
        $dt->order_by( '`email`', '`billing_first_name`', '`date_registered`' );

        $website_users = $this->website_user->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $website_users ) instanceof WebsiteUser );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
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
        $dt->order_by( '`email`', '`billing_first_name`', '`date_registered`' );

        $count = $this->website_user->count_all( $dt->get_count_variables() );

        // Make sure they exist
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
