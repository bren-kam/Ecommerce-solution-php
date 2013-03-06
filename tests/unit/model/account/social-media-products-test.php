<?php

require_once 'base-database-test.php';

class SocialMediaProductsTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaProducts
     */
    private $sm_products;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_products = new SocialMediaProducts();
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
        $this->sm_products = null;
    }
}
