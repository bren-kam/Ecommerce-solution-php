<?php

require_once 'base-database-test.php';

class AccountTest extends BaseDatabaseTest {
    /**
     * @var Account
     */
    private $account;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account = new Account();
    }

    /**
     * Test listing all accounts
     */
    public function testListAll() {
        //$accounts = $this->account->list_all( $user);
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account = null;
    }
}
