<?php

require_once 'base-database-test.php';

class AccountProductGroupTest extends BaseDatabaseTest {
    /**
     * @var AccountProductGroup
     */
    private $account_product_group;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_product_group = new AccountProductGroup();
    }

    /**
     * Test Adding bulk products
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product_group = null;
    }
}
