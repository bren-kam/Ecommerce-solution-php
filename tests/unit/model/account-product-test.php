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

    /**
     * Test Adding bulk products
     */
    public function testAddBulk() {
        // Declare variables
        $account_id = -2;
        $industry_ids = array( 1 );
        $skus = array( '2010', '2470', '010-433' ); // 010-433 has two products -- we should only get one

        $this->account_product->add_bulk( $account_id, $industry_ids, $skus );

        // Lets get the products
        $product_ids = $this->db->get_results( 'SELECT `product_id` FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertEquals( 3, count( $product_ids ) );

        // Delete
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Test copy by account
     */
    public function testCopyByAccount() {
        // Declare variables
        $template_account_id = 160; // Connell's account
        $account_id = -3;

        // Make sure there are no products to start with
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );

        // Copy by account
        $this->account_product->copy_by_account( $template_account_id, $account_id );

        // Lets get the products
        $product_ids = $this->db->get_results( 'SELECT `product_id` FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertGreaterThan( 0, count( $product_ids ) );

        // Delete them
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Test Getting an attribute item
     *
     * @depends testCopyByAccount
     */
    public function testDeactivateByAccount() {
        // Declare variables
        $template_account_id = 160; // Connell's account
        $account_id = -2;

        $this->account_product->copy_by_account( $template_account_id, $account_id );

        // Now, deactivate them all
        $this->account_product->deactivate_by_account( $account_id );

        // Lets get the products
        $product_ids = $this->db->get_results( 'SELECT `product_id` FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertEquals( count( $product_ids ), 0 );
    }

    /**
     * Test removing bulk items
     *
     * @depends testAddBulk
     */
    public function testRemoveBulk() {
        // Declare variables
        $bulk_items_product_ids = array( 17, 1899, 37370 );
        $account_id = -2;
        $industry_ids = array( 1 );
        $skus = array( '2010', '2470', '010-433' ); // 010-433 has two products -- we should only get one

        // Add bulk
        $this->account_product->add_bulk( $account_id, $industry_ids, $skus );

        // Remove bulk
        $this->account_product->remove_bulk( $account_id, $bulk_items_product_ids );

        // Lets get the products
        $product_ids = $this->db->get_results( 'SELECT `product_id` FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertEquals( count( $product_ids ), 0 );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }
}
