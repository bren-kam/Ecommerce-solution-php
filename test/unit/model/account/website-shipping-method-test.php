<?php

require_once 'test/base-database-test.php';

class WebsiteShippingMethodTest extends BaseDatabaseTest {
    const NAME = 'In-store Pickup';

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

        // Define
        $this->phactory->define( 'website_shipping_methods', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME ) );
        $this->phactory->recall();
    }


    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_website_shipping_method = $this->phactory->create('website_shipping_methods');

        // Get
        $this->website_shipping_method->get( $ph_website_shipping_method->website_shipping_method_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->website_shipping_method->name );
    }

    /**
     * Get By Account
     */
    public function testGetByAccount() {
        // Create
        $this->phactory->create('website_shipping_methods');

        // Get
        $website_shipping_methods = $this->website_shipping_method->get_by_account( self::WEBSITE_ID );
        $website_shipping_method = current( $website_shipping_methods );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteShippingMethod', $website_shipping_methods );
        $this->assertEquals( self::NAME, $website_shipping_method->name );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->website_shipping_method->name = self::NAME;
        $this->website_shipping_method->create();

        // Assert
        $this->assertNotNull( $this->website_shipping_method->id );

        // Get
        $ph_website_shipping_method = $this->phactory->get( 'website_shipping_methods', array( 'website_shipping_method_id' => $this->website_shipping_method->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_website_shipping_method->name );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_website_shipping_method = $this->phactory->create('website_shipping_methods');

        // Save
        $this->website_shipping_method->id = $ph_website_shipping_method->website_shipping_method_id;
        $this->website_shipping_method->name = '10 Mile Radius';
        $this->website_shipping_method->save();

        // Get
        $ph_website_shipping_method = $this->phactory->get( 'website_shipping_methods', array( 'website_shipping_method_id' => $ph_website_shipping_method->website_shipping_method_id ) );

        // Assert
        $this->assertEquals( $this->website_shipping_method->name, $ph_website_shipping_method->name );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $ph_website_shipping_method = $this->phactory->create('website_shipping_methods');

        // Delete
        $this->website_shipping_method->id = $ph_website_shipping_method->website_shipping_method_id;
        $this->website_shipping_method->remove();

        // Get
        $ph_website_shipping_method = $this->phactory->get( 'website_shipping_methods', array( 'website_shipping_method_id' => $ph_website_shipping_method->website_shipping_method_id ) );

        // Assert
        $this->assertNull( $ph_website_shipping_method );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_shipping_methods');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`method`', '`amount`' );

        // Get
        $website_shipping_methods = $this->website_shipping_method->list_all( $dt->get_variables() );
        $website_shipping_method = current( $website_shipping_methods );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteShippingMethod', $website_shipping_methods );
        $this->assertEquals( self::NAME, $website_shipping_method->name );

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
        $this->phactory->create('website_shipping_methods');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`method`', '`amount`' );

        // Get
        $count = $this->website_shipping_method->count_all( $dt->get_count_variables() );

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
        $this->website_shipping_method = null;
    }
}
