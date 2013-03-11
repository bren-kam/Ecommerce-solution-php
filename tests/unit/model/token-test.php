<?php

require_once 'base-database-test.php';

class TokenTest extends BaseDatabaseTest {
    /**
     * @var Token
     */
    private $token;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->token = new Token();
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
        $this->token = null;
    }
}
