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

    /**
     * Test delete by account
     */
    public function testDeleteByAccount() {
        // Declare variables
        $account_id = -5;

        // Insert categories
        $this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id` ) VALUES ( $account_id, -1 ), ( $account_id, -2 ), ( $account_id, -3 )" );

        // Make sure they exist
        $category_ids = $this->db->get_col( "SELECT `category_id` FROM `website_categories` WHERE `website_id` = $account_id" );

        $this->assertEquals( 3, count( $category_ids ) );

        // Delete
        $this->account_category->delete_by_account( $account_id );

        // Make sure they're not there
        $category_ids = $this->db->get_col( "SELECT `category_id` FROM `website_categories` WHERE `website_id` = $account_id" );

        $this->assertEquals( 0, count( $category_ids ) );
    }

    /**
     * Test reorganizing categories
     *
     * @depends testDeleteByAccount
     */
    public function testReorganizeCategories() {
        // Declare variables
        $account_id = 96; // Testing

        // Delete all website products
        $this->account_category->delete_by_account( $account_id );

        // Insert bad categories
        $this->db->query( "INSERT INTO `website_categories` ( `website_id`, `category_id` ) VALUES ( $account_id, -1 ), ( $account_id, -2 ), ( $account_id, -3 )" );

        // Reorganize
        $this->account_category->reorganize_categories( $account_id, new Category() );

        // check again
        $category_ids = $this->db->get_col( "SELECT `category_id` FROM `website_categories` WHERE `website_id` = $account_id" );

        $this->assertGreaterThan( 0, $category_ids );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_category = null;
    }
}
