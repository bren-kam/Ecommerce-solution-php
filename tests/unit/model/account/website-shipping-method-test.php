<?php

require_once 'base-database-test.php';

class WebsiteShippingMethodTest extends BaseDatabaseTest {
    /**
     * @var WebsiteShippingMethod
     */
    private $website_shipping_method;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_shipping_method = new WebsiteShippingMethod();
    }
    
    /**
     * Test Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $name = 'Grandma Shipping';

        // Create
        $website_shipping_method_id = $this->db->insert( 'website_shipping_methods', compact( 'website_id', 'name' ), 'is' );

        // Get
        $this->website_shipping_method->get( $website_shipping_method_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $name, $this->website_shipping_method->name );

        // Clean up
        $this->db->delete( 'website_shipping_methods', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Get By Account
     */
    public function testGetByAccount() {
        // Set variables
        $website_id = -7;
        $name = 'Grandma Shipping';

        // Create
        $this->db->insert( 'website_shipping_methods', compact( 'website_id', 'name' ), 'is' );

        // Get all
        $website_shipping_methods = $this->website_shipping_method->get_by_account( $website_id );

        $this->assertTrue( current( $website_shipping_methods ) instanceof WebsiteShippingMethod );

        // Clean up
        $this->db->delete( 'website_shipping_methods', compact( 'website_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $website_id = -5;
        $name = 'Grandma Shipping';

        // Create
        $this->website_shipping_method->website_id = $website_id;
        $this->website_shipping_method->name = $name;
        $this->website_shipping_method->create();

        // Make sure it's in the database
        $retrieved_name = $this->db->get_var( "SELECT `name` FROM `website_shipping_methods` WHERE `website_id` = $website_id" );

        $this->assertEquals( $name, $retrieved_name );

        // Delete
        $this->db->delete( 'website_shipping_methods', compact( 'website_id' ), 'i' );
    }

    /**
         * Save
         *
         * @depends testCreate
         */
        public function testSave() {
            // Declare variables
            $website_id = -5;
            $name = 'Grandma Shipping';

            // Create
            $this->website_shipping_method->website_id = $website_id;
            $this->website_shipping_method->create();

            // Get
            $this->website_shipping_method->name = $name;
            $this->website_shipping_method->save();

            // Make sure it's in the database
            $retrieved_name = $this->db->get_var( "SELECT `name` FROM `website_shipping_methods` WHERE `website_id` = $website_id" );

            $this->assertEquals( $name, $retrieved_name );

            // Clean up
            $this->db->delete( 'website_shipping_methods', compact( 'website_id' ), 'i' );
        }

    /**
     * Remove
     */
    public function testRemove() {
        // Declare variables
        $website_id = -5;
        $name = 'Grandma Shipping';

        // Create
        $this->website_shipping_method->website_id = $website_id;
        $this->website_shipping_method->name = $name;
        $this->website_shipping_method->create();

        // Get
        $this->website_shipping_method->remove();

        // Make sure it's in the database
        $retrieved_name = $this->db->get_var( "SELECT `name` FROM `website_shipping_methods` WHERE `website_id` = $website_id" );

        $this->assertFalse( $retrieved_name );
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
        $dt->order_by( '`name`', '`method`', '`amount`' );

        $website_shipping_methods = $this->website_shipping_method->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $website_shipping_methods ) instanceof WebsiteShippingMethod );

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
        $dt->order_by( '`name`', '`method`', '`amount`' );

        $count = $this->website_shipping_method->count_all( $dt->get_count_variables() );

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
        $this->website_shipping_method = null;
    }
}
