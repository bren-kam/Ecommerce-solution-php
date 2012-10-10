<?php

require_once 'base-database-test.php';

class EmailMessageTest extends BaseDatabaseTest {
    /**
     * @var EmailMessage
     */
    private $email_message;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_message = new EmailMessage();
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
        $this->email_message = null;
    }
}
