<?php

require_once 'test/base-database-test.php';

class WebsiteOrderTest extends BaseDatabaseTest {
    /**
     * @var WebsiteOrder
     */
    private $website_order;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_order = new WebsiteOrder();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $name = 'Grandma Shipping';
        $website_id = -5;
        $billing_first_name = 'John';

        // Create website shipping method
        $website_shipping_method_id = $this->phactory->insert( 'website_shipping_methods', compact( 'name' ), 's' );
        $website_order_id = $this->phactory->insert( 'website_orders', compact( 'website_id', 'website_shipping_method_id', 'billing_first_name' ), 'iis' );

        // Get
        $this->website_order->get( $website_order_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $billing_first_name, $this->website_order->billing_first_name );

        // Clean up
        $this->phactory->delete( 'website_orders', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'website_shipping_methods', compact( 'website_shipping_method_id' ), 'i' );
    }

    /**
     * Get Complete
     */
    public function testGetComplete() {
        // Declare variables
        $name = 'Grandma Shipping';
        $website_id = -5;
        $billing_first_name = 'John';
        $items = array( 'Delicate Love Seat', 'Amelie Couch' );

        // Create website shipping method
        $website_shipping_method_id = $this->phactory->insert( 'website_shipping_methods', compact( 'name' ), 's' );
        $website_order_id = $this->phactory->insert( 'website_orders', compact( 'website_id', 'website_shipping_method_id', 'billing_first_name' ), 'iis' );

        // Get mock
        $stub_website_order_item = $this->getMock( 'WebsiteOrderItem' );
        $stub_website_order_item->expects($this->once())->method('get_all')->with( $website_order_id )->will($this->returnValue( $items ) );

        // Get
        $this->website_order->get_complete( $website_order_id, $website_id, $stub_website_order_item );

        // Make sure we grabbed the right one
        $this->assertEquals( $billing_first_name, $this->website_order->billing_first_name );
        $this->assertEquals( $items, $this->website_order->items );

        // Clean up
        $this->phactory->delete( 'website_orders', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'website_shipping_methods', compact( 'website_shipping_method_id' ), 'i' );
    }
    
    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Set variables
        $website_id = -7;
        $status = -5;

        // Create
        $website_order_id = $this->phactory->insert( 'website_orders', compact( 'website_id' ), 'i' );

        // Get
        $this->website_order->get( $website_order_id, $website_id );

        // Set status
        $this->website_order->status = -5;
        $this->website_order->save();

        // Now check it!
        $retrieved_status = $this->phactory->get_var( "SELECT `status` FROM `website_orders` WHERE `website_order_id` = $website_order_id" );

        $this->assertEquals( $retrieved_status, $status );

        // Clean up
        $this->phactory->delete( 'website_orders', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Set variables
        $website_id = -7;
        $billing_first_name = 'John';

        // Create
        $website_order_id = $this->phactory->insert( 'website_orders', compact( 'website_id', 'billing_first_name' ), 'is' );
        
        // Get
        $this->website_order->get( $website_order_id, $website_id );

        // Remove/Delete
        $this->website_order->remove();

        $retrieved_billing_first_name = $this->phactory->get_var( "SELECT `billing_first_name` FROM `website_orders` WHERE `website_order_id` = $website_order_id" );

        // Clean Up
        $this->assertFalse( $retrieved_billing_first_name );
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
        $dt->order_by( '`website_order_id`', '`total_cost`', '`status`', '`date_created`' );

        $website_orders = $this->website_order->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $website_orders ) instanceof WebsiteOrder );

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
        $dt->order_by( '`website_order_id`', '`total_cost`', '`status`', '`date_created`' );

        $count = $this->website_order->count_all( $dt->get_count_variables() );

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
        $this->website_order = null;
    }
}
