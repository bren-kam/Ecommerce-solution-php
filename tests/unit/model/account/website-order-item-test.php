<?php

require_once 'base-database-test.php';

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
     * Test
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order_item = null;
    }
}
