<?php

require_once 'test/base-database-test.php';

class WebsiteOrderItemTest extends BaseDatabaseTest {
    const WEBSITE_ORDER_ID = 5;
    const NAME = 'White Paint';

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

        // Define
        $this->phactory->define( 'website_order_items', array( 'website_order_id' => self::WEBSITE_ORDER_ID, 'name' => self::NAME ) );
        $this->phactory->define('products');
        $this->phactory->define('industries');
        $this->phactory->recall();
    }

    /**
     * Get By Order
     */
    public function testGetByOrder() {
        // Create
        $ph_industry = $this->phactory->create('industries');
        $ph_product = $this->phactory->create( 'products', array( 'industry_id' => $ph_industry->industry_id ) );
        $this->phactory->create( 'website_order_items', array( 'product_id' => $ph_product->product_id ) );

        // Get
        $website_order_items = $this->website_order_item->get_by_order( self::WEBSITE_ORDER_ID );
        $website_order_item = current( $website_order_items );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteOrderItem', $website_order_items );
        $this->assertEquals( self::NAME, $website_order_item->name );
    }

    /**
     * Get All
     *
     * @depends testGetByOrder
     */
    public function testGetAll() {
        // Create
        $ph_industry = $this->phactory->create('industries');
        $ph_product = $this->phactory->create( 'products', array( 'industry_id' => $ph_industry->industry_id ) );
        $ph_website_order_item = $this->phactory->create( 'website_order_items', array( 'product_id' => $ph_product->product_id ) );

        // Stubs
        $website_order_item_option = $this->getMock( 'WebsiteOrderItemOption' );
        $website_order_item_option->website_order_item_id = $ph_website_order_item->website_order_item_id;

        $stub_website_order_item_option = $this->getMock( 'WebsiteOrderItemOption' );
        $stub_website_order_item_option->expects($this->once())->method('get_by_order')->with( self::WEBSITE_ORDER_ID )->will($this->returnValue( array( $website_order_item_option ) ) );

        // Get
        $website_order_items = $this->website_order_item->get_all( self::WEBSITE_ORDER_ID, $stub_website_order_item_option );
        $website_order_item = current( $website_order_items );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteOrderItem', $website_order_items );
        $this->assertEquals( self::NAME, $website_order_item->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order_item = null;
    }
}
