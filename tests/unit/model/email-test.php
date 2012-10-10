<?php

require_once 'base-database-test.php';

class EmailTest extends BaseDatabaseTest {
    /**
     * @var Email
     */
    private $email;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email = new Email();
    }

    /**
     * Test method
     */
    public function testMethod() {
        // Do stuff
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email = null;
    }
}
