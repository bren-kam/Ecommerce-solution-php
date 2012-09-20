<?php

require_once 'base-database-test.php';

class AccountProductTest extends BaseDatabaseTest {
    /**
     * @var AccountProduct
     */
    private $account_product;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_product = new AccountProduct();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }
}
