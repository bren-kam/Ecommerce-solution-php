<?php

require_once 'base-database-test.php';

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
     * Test
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_order = null;
    }
}
