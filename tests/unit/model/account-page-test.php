<?php

require_once 'base-database-test.php';

class AccountPageTest extends BaseDatabaseTest {
    /**
     * @var AccountPage
     */
    private $account_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page = new AccountPage();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_page = null;
    }
}
