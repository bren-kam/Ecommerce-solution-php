<?php

require_once 'base-database-test.php';

class AccountPagemetaTest extends BaseDatabaseTest {
    /**
     * @var AccountPagemeta
     */
    private $account_pagemeta;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_pagemeta = new AccountPagemeta();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_pagemeta = null;
    }
}
