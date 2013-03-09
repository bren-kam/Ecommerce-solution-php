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
     * Get With List Items
     */
    public function testGetWithListItems() {
        // Declare variables
        $website_id = -5;
        $product_id = -3;

        // Get the notes
        $notes = $this->account_product_option->get_with_list_items( $website_id, $product_id );

        $this->assertTrue( current( $notes ) instanceof AccountProductOption );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->account_product_option = null;
    }
}
