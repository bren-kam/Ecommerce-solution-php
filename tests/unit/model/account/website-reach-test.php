<?php

require_once 'base-database-test.php';

class WebsiteReachTest extends BaseDatabaseTest {
    /**
     * @var WebsiteReach
     */
    private $website_reach;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_reach = new WebsiteReach();
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
        $this->website_reach = null;
    }
}
