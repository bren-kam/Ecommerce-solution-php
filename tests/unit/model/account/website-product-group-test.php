<?php

require_once 'base-database-test.php';

class WebsiteProductGroupTest extends BaseDatabaseTest {
    /**
     * @var WebsiteProductGroup
     */
    private $website_product_group;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_product_group = new WebsiteProductGroup();
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
        $this->website_product_group = null;
    }
}
