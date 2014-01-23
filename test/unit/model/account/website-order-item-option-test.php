<?php

require_once 'test/base-database-test.php';

class WebsiteOrderItemOptionTest extends BaseDatabaseTest {
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
    }

    /**
     * Get By Order
     */
    public function testGetByOrder() {
        // Set variables
        $website_order_id = -3;

        // Insert website order item
        $website_order_item_id = $this->phactory->insert( 'website_order_items', array(
            'website_order_id' => $website_order_id
        ), 'i' );

        // Create option
        $this->phactory->insert( 'website_order_item_options', array(
            'website_order_item_id' => $website_order_item_id
        ), 'i' );

        // Get options
        $options = $this->website_order_item_option->get_by_order( $website_order_id );

        $this->assertTrue( current( $options ) instanceof WebsiteOrderItemOption );

        // Clean up
        $this->phactory->delete( 'website_order_items', array( 'website_order_id' => $website_order_id ), 'i' );
        $this->phactory->delete( 'website_order_item_options', array( 'website_order_item_id' => $website_order_item_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order_item_option = null;
    }
}
