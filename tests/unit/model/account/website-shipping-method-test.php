<?php

require_once 'base-database-test.php';

class WebsiteShippingMethodTest extends BaseDatabaseTest {
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
        $this->website_shipping_method = null;
    }
}
