<?php

require_once 'test/base-database-test.php';

class WebsiteLocationTest extends BaseDatabaseTest {
    /**
     * @var WebsiteLocation
     */
    private $website_location;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_location = new WebsiteLocation();
    }

    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_location = null;
    }
}
