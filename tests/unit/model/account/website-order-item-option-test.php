<?php

require_once 'base-database-test.php';

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
     * Test
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order_item_option = null;
    }
}
