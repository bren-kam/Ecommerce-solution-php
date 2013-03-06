<?php

require_once 'base-database-test.php';

class WebsiteUserTest extends BaseDatabaseTest {
    /**
     * @var WebsiteUser
     */
    private $website_user;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_user = new WebsiteUser();
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
        $this->website_user = null;
    }
}
