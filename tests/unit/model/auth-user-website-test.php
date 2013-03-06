<?php

require_once 'base-database-test.php';

class AuthUserWebsiteTest extends BaseDatabaseTest {
    /**
     * @var AuthUserWebsite
     */
    private $auth_user_website;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->auth_user_website = new AuthUserWebsite();
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
        $this->auth_user_website = null;
    }
}
