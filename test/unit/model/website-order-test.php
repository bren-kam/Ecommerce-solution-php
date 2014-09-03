<?php

require_once 'test/base-database-test.php';

class WebsiteOrderTest extends BaseDatabaseTest {
    const BILLING_FIRST_NAME = 'Bill Waters';

    /**
     * @var WebsiteOrder
     */
    private $website_order;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->website_order = new WebsiteOrder();

        // Define
        $this->phactory->define( 'website_orders', array( 'website_id' => self::WEBSITE_ID, 'billing_first_name' => self::BILLING_FIRST_NAME, 'status' => WebsiteOrder::STATUS_PENDING ) );
        $this->phactory->recall();
    }

    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_order = $this->phactory->create('website_orders');

        // Get
        $this->website_order->get( $ph_website_order->website_order_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::BILLING_FIRST_NAME, $this->website_order->billing_first_name );
    }

    /**
     * Get Complete
     */
    public function testGetComplete() {
        // Declare
        $items = array( 'Delicate Love Seat', 'Amelie Couch' );

        // Create
        $ph_website_order = $this->phactory->create('website_orders');

        // Stubs
        $stub_website_order_item = $this->getMock( 'WebsiteOrderItem' );
        $stub_website_order_item->expects($this->once())->method('get_all')->with( $ph_website_order->website_order_id )->will($this->returnValue( $items ) );

        // Get
        $this->website_order->get_complete( $ph_website_order->website_order_id, self::WEBSITE_ID, $stub_website_order_item );

        // Make sure we grabbed the right one
        $this->assertEquals( self::BILLING_FIRST_NAME, $this->website_order->billing_first_name );
        $this->assertEquals( $items, $this->website_order->items );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_website_order = $this->phactory->create('website_orders');

        // Save
        $this->website_order->id = $ph_website_order->website_order_id;
        $this->website_order->status = WebsiteOrder::STATUS_DECLINED;
        $this->website_order->save();

        // Get
        $ph_website_order = $this->phactory->get( 'website_orders', array( 'website_order_id' => $ph_website_order->website_order_id ) );

        // Assert
        $this->assertEquals( $this->website_order->status, $ph_website_order->status );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Create
        $ph_website_order = $this->phactory->create('website_orders');

        // Delete
        $this->website_order->id = $ph_website_order->website_order_id;
        $this->website_order->remove();

        // Get
        $ph_website_order = $this->phactory->get( 'website_orders', array( 'website_order_id' => $ph_website_order->website_order_id ) );

        // Assert
        $this->assertNull( $ph_website_order );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_orders');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`website_order_id`', '`total_cost`', '`status`', '`date_created`' );

        // Get
        $website_orders = $this->website_order->list_all( $dt->get_variables() );
        $website_order = current( $website_orders );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteOrder', $website_orders );
        $this->assertEquals( WebsiteOrder::STATUS_PENDING, $website_order->status );

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
        $this->phactory->create('website_orders');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`website_order_id`', '`total_cost`', '`status`', '`date_created`' );

        // Get
        $count = $this->website_order->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->website_order = null;
    }
}
