<?php

require_once 'base-database-test.php';

class AccountCategoryTest extends BaseDatabaseTest {
    /**
     * @var AccountCategory
     */
    private $account_category;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_category = new AccountCategory();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_category = null;
    }
}
