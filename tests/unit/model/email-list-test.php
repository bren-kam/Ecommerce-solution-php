<?php

require_once 'base-database-test.php';

class EmailListTest extends BaseDatabaseTest {
    /**
     * @var EmailList
     */
    private $email_list;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_list = new EmailList();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_list = null;
    }
}
