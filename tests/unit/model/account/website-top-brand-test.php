<?php

require_once 'base-database-test.php';

class WebsiteTopBrandTest extends BaseDatabaseTest {
    /**
     * @var WebsiteTopBrand
     */
    private $website_top_brand;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_top_brand = new WebsiteTopBrand();
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
        $this->website_top_brand = null;
    }
}
