<?php

require_once 'base-database-test.php';

class AccountProductOptionTest extends BaseDatabaseTest {
    /**
     * @var AccountProductOption
     */
    private $account_product_option;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->account_product_option = new AccountProductOption();
    }

    /**
     * Test
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->account_product_option = null;
    }
}
