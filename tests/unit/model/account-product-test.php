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

        $this->db->insert( 'products', array( 'product_id' => -3, 'industry_id' => 1, 'sku' => '2010', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'product_id' => -4, 'industry_id' => 1, 'sku' => '2470', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'product_id' => -5, 'industry_id' => 1, 'sku' => '2470', 'publish_visibility' => 'public' ), 'iiss' );
        $skus = array( '2010', '2470' ); // 2470 has two products -- we should only get one

        $this->account_product->add_bulk( $account_id, $industry_ids, $skus );

        // Lets get the products
        $count = $this->db->get_var( 'SELECT COUNT( `product_id` ) FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertEquals( 2, $count );

        // Delete
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'products', array( 'product_id' => -3 ), 'i' );
        $this->db->delete( 'products', array( 'product_id' => -4 ), 'i' );
        $this->db->delete( 'products', array( 'product_id' => -5 ), 'i' );
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
        $bulk_items_product_ids = array( -3, -4, -5 );
        $account_id = -2;
        $industry_ids = array( 1 );
        $skus = array( '2010', '2470' ); // 2470 has two products -- we should only get one

        $this->db->insert( 'products', array( 'product_id' => -3, 'industry_id' => 1, 'sku' => '2010', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'product_id' => -4, 'industry_id' => 1, 'sku' => '2470', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'product_id' => -5, 'industry_id' => 1, 'sku' => '2470', 'publish_visibility' => 'public' ), 'iiss' );

        // Add bulk
        $this->account_product->add_bulk( $account_id, $industry_ids, $skus );

        // Remove bulk
        $this->account_product->remove_bulk( $account_id, $bulk_items_product_ids );

        // Lets get the products
        $product_id_count = $this->db->get_var( 'SELECT COUNT( `product_id` ) FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertEquals( 0, $product_id_count );
    }

    /**
     * Test removing all products from accounts by an id
     */
    public function testDeleteByProduct() {
        // Declare variables
        $product_id = -5;
        $account_id1 = -3;
        $account_id2 = -2;

        // Insert into accounts
        $this->db->insert( 'website_products', array( 'product_id' => $product_id, 'website_id' => $account_id1, 'active' => 1 ), 'iii' );
        $this->db->insert( 'website_products', array( 'product_id' => $product_id, 'website_id' => $account_id2, 'active' => 1 ), 'iii' );

        // Delete them
        $this->account_product->delete_by_product( $product_id );

        // We should be able to get them
        $active = $this->db->get_col( "SELECT `active` FROM `website_products` WHERE `product_id` = $product_id" );

        $this->assertEquals( 2, count( $active ) );
        $this->assertEquals( '0', $active[0] );

        // Delete them
        $this->db->delete( 'website_products', array( 'product_id' => $product_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }
}
