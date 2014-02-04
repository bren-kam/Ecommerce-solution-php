<?php

require_once 'test/base-database-test.php';

class WebsiteOrderItemTest extends BaseDatabaseTest {
    /**
     * @var WebsiteOrderItem
     */
    private $website_order_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_order_item = new WebsiteOrderItem();
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Get By Order
//     */
//    public function testGetByOrder() {
//        // Declare variables
//        $website_order_id = -5;
//        $image = 'trans.gif';
//        $industry = 'Paint';
//        $name = 'White Paint';
//
//        // Create order
//        $industry_id = $this->phactory->insert( 'industries', array( 'name' => $industry ), 's' );
//        $product_id = $this->phactory->insert( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->insert( 'product_images', compact( 'product_id', 'image' ), 'is' );
//        $this->phactory->insert( 'website_order_items', compact( 'website_order_id', 'product_id', 'name' ), 'iis' );
//
//        // Get by order
//        $website_order_items = $this->website_order_item->get_by_order( $website_order_id );
//
//        $this->assertTrue( current( $website_order_items ) instanceof WebsiteOrderItem );
//
//        // Clean up
//        $this->phactory->delete( 'industries', compact( 'industry_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'product_id' ), 'i' );
//        $this->phactory->delete( 'product_images', compact( 'product_id' ), 'i' );
//        $this->phactory->delete( 'website_order_items', compact( 'website_order_id' ), 'i' );
//    }
//
//    /**
//     * Get All
//     *
//     * @depends testGetByOrder
//     */
//    public function testGetAll() {
//        // Declare variables
//        $website_order_id = -5;
//        $image = 'trans.gif';
//        $industry = 'Paint';
//        $name = 'White Paint';
//
//        // Create order
//        $industry_id = $this->phactory->insert( 'industries', array( 'name' => $industry ), 's' );
//        $product_id = $this->phactory->insert( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->insert( 'product_images', compact( 'product_id', 'image' ), 'is' );
//        $website_order_item_id = $this->phactory->insert( 'website_order_items', compact( 'website_order_id', 'product_id', 'name' ), 'iis' );
//
//        // Create stubs
//        $website_order_item_option = $this->getMock( 'WebsiteOrderItemOption' );
//        $website_order_item_option->website_order_item_id = $website_order_item_id;
//
//        $stub_website_order_item_option = $this->getMock( 'WebsiteOrderItemOption' );
//        $stub_website_order_item_option->expects($this->once())->method('get_by_order')->with( $website_order_id )->will($this->returnValue( array( $website_order_item_option ) ) );
//
//        // Get order items
//        $website_order_items = $this->website_order_item->get_all( $website_order_id, $stub_website_order_item_option );
//
//        // Get complete
//        $this->assertTrue( current( $website_order_items ) instanceof WebsiteOrderItem );
//
//        // Clean up
//        $this->phactory->delete( 'industries', compact( 'industry_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'product_id' ), 'i' );
//        $this->phactory->delete( 'product_images', compact( 'product_id' ), 'i' );
//        $this->phactory->delete( 'website_order_items', compact( 'website_order_id' ), 'i' );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order_item = null;
    }
}
