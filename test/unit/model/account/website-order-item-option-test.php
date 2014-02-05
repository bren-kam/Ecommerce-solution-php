<?php

require_once 'test/base-database-test.php';

class WebsiteOrderItemOptionTest extends BaseDatabaseTest {
    const WEBSITE_ORDER_ITEM_ID = 5;
    const OPTION_NAME = 'Queen Mattress';

    // Website Order Item
    const WEBSITE_ORDER_ID = 13;

    /**
     * @var WebsiteOrderItemOption
     */
    private $website_order_item_option;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_order_item_option = new WebsiteOrderItemOption();

        // Define
        $this->phactory->define( 'website_order_item_options', array( 'website_order_item_id' => self::WEBSITE_ORDER_ITEM_ID, 'option_name' => self::OPTION_NAME ) );
        $this->phactory->define( 'website_order_items', array( 'website_order_id' => self::WEBSITE_ORDER_ID ) );
        $this->phactory->recall();
    }


    /**
     * Get By Order
     */
    public function testGetByOrder() {
        // Create
        $ph_website_order_item = $this->phactory->create('website_order_items');
        $this->phactory->create( 'website_order_item_options', array( 'website_order_item_id' => $ph_website_order_item->website_order_item_id ) );

        // Get
        $website_order_item_options = $this->website_order_item_option->get_by_order( self::WEBSITE_ORDER_ID );
        $website_order_item_option = current( $website_order_item_options );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteOrderItemOption', $website_order_item_options );
        $this->assertEquals( self::OPTION_NAME, $website_order_item_option->option_name );

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order_item_option = null;
    }
}
