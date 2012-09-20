<?php

require_once 'base-database-test.php';

class EmailAutoresponderTest extends BaseDatabaseTest {
    /**
     * @var EmailAutoresponder
     */
    private $email_autoresponder;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_autoresponder = new EmailAutoresponder();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_autoresponder = null;
    }
}
