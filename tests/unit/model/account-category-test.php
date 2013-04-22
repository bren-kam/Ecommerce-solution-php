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
     * Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $name = 'elbow pads';
        $status = 1;

        // Create
        $category_id = $this->db->insert( 'categories', compact( 'name' ), 's' );
        $this->db->insert( 'website_categories', compact( 'category_id', 'website_id', 'status' ), 'iii' );

        // Get
        $this->account_category->get( $website_id, $category_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $name, $this->account_category->title );

        // Clean up
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'website_categories', compact( 'website_id' ), 'i' );
    }

    /**
     * Get All Ids
     */
    public function testGetAllIds() {
        // Set variables
        $website_id = -7;
        $category_id = -5;
        $status = 1;

        // Create
        $this->db->insert( 'website_categories', compact( 'category_id', 'website_id', 'status' ), 'iii' );
        $category_id = -5;
        $this->db->insert( 'website_categories', compact( 'category_id', 'website_id', 'status' ), 'iii' );

        // Get
        $category_ids = $this->account_category->get_all_ids( $website_id );

        // Make sure we grabbed the right one
        $this->assertTrue( in_array( $category_id, $category_ids ) );

        // Clean up
        $this->db->delete( 'website_categories', compact( 'website_id' ), 'i' );
    }

    /**
     * Get Blocked Website Category Ids
     */
    public function testGetBlockedWebsiteCategoryIds() {
        // Set variables
        $website_id = -7;
        $category_id = -5;

        // Create
        $this->db->insert( 'website_blocked_category', compact( 'category_id', 'website_id' ), 'ii' );
        $category_id = -5;
        $this->db->insert( 'website_blocked_category', compact( 'category_id', 'website_id' ), 'ii' );

        // Get
        $blocked_website_category_ids = $this->account_category->get_blocked_website_category_ids( $website_id );

        // Make sure we grabbed the right one
        $this->assertTrue( in_array( $category_id, $blocked_website_category_ids ) );

        // Clean up
        $this->db->delete( 'website_blocked_category', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Set variables
        $website_id = -7;
        $category_id = -5;
        $title = 'Helllo!';

        // Create
        $this->db->insert( 'website_categories', compact( 'website_id', 'category_id' ), 'ii' );

        // Get
        $this->account_category->get( $website_id, $category_id );
        $this->account_category->title = $title;
        $this->account_category->save();

        // Now check it!
        $retrieved_title = $this->db->get_var( "SELECT `title` FROM `website_categories` WHERE `website_id` = $website_id AND `category_id` = $category_id" );

        $this->assertEquals( $retrieved_title, $title );

        // Clean up
        $this->db->delete( 'website_categories', compact( 'website_id' ), 'i' );
    }

    /**
     * Hide
     */
    public function testHide() {
         // Set variables
        $website_id = -7;
        $category_ids = array( -3, -5, -9 );

        // Hide
        $this->account_category->hide( $website_id, $category_ids );

        // Check IDS
        $retrieved_category_ids = $this->db->get_col( "SELECT `category_id` FROM `website_blocked_category` WHERE `website_id` = $website_id ORDER BY `category_id` DESC" );

        $this->assertEquals( $retrieved_category_ids, $category_ids );

        // Clean up
        $this->db->delete( 'website_blocked_category', compact( 'website_id' ), 'i' );
    }

    /**
     * Unhide
     *
     * @depends testHide
     */
    public function testUnhide() {
         // Set variables
        $website_id = -7;
        $category_ids = array( -3, -5, -9 );

        // Hide
        $this->account_category->hide( $website_id, $category_ids );

        // Unhide
        $this->account_category->unhide( $website_id, $category_ids );

        // Check it
        $retrieved_category_ids = $this->db->get_col( "SELECT `category_id` FROM `website_blocked_category` WHERE `website_id` = $website_id ORDER BY `category_id` DESC" );

        $this->assertEquals( $retrieved_category_ids, array() );
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
     * List All
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'title', 'wc.`date_updated`' );

        $website_categories = $this->account_category->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $website_categories ) instanceof AccountCategory );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'title', 'wc.`date_updated`' );

        $count = $this->account_category->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_category = null;
    }
}
