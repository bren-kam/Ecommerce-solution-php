<?php

require_once 'base-database-test.php';

class EmailMarketingTest extends BaseDatabaseTest {
    /**
     * @var EmailMarketing
     */
    private $email_marketing;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_marketing = new EmailMarketing();
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
        $this->email_marketing = null;
    }
}
