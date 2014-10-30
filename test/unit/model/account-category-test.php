<?php

require_once 'test/base-database-test.php';

class AccountCategoryTest extends BaseDatabaseTest {
    const TITLE = 'Test Category';
    const CATEGORY_NAME = 'elbow pads';
    const CATEGORY_ID = 5;

    /**
     * @var AccountCategory
     */
    private $account_category;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_category = new AccountCategory();

        // Define
        $this->phactory->define( 'categories', array( 'name' => self::CATEGORY_NAME ) );
        $this->phactory->define( 'website_blocked_category', array( 'website_id' => self::WEBSITE_ID, 'category_id' => self::CATEGORY_ID ) );
        $this->phactory->define( 'website_categories', array( 'website_id' => self::WEBSITE_ID, 'category_id' => self::CATEGORY_ID, 'title' => self::TITLE ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $this->phactory->create( 'website_categories', array( 'category_id' => $ph_category->category_id ) );

        // Get
        $this->account_category->get( self::WEBSITE_ID, $ph_category->category_id );

        // Make sure we grabbed the right one
        $this->assertEquals( self::TITLE, $this->account_category->title );
    }

    /**
     * Get All Ids
     */
    public function testGetAllIds() {
        // Create
        $this->phactory->create( 'website_categories' );

        // Get
        $category_ids = $this->account_category->get_all_ids( self::WEBSITE_ID );

        // Make sure we grabbed the right one
        $this->assertContains( self::CATEGORY_ID, $category_ids );
    }

    /**
     * Get Blocked Website Category Ids
     */
    public function testGetBlockedWebsiteCategoryIds() {
        // Create
        $this->phactory->create( 'website_categories' );
        $this->phactory->create( 'website_blocked_category' );

        // Get
        $blocked_website_category_ids = $this->account_category->get_blocked_website_category_ids( self::WEBSITE_ID );

        // Make sure we grabbed the right one
        $this->assertContains( self::CATEGORY_ID, $blocked_website_category_ids );
    }

    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Declare
        $title = 'Woot!';

        // Create
        $this->phactory->create( 'website_categories' );

        // Get
        $this->account_category->get( self::WEBSITE_ID, self::CATEGORY_ID );
        $this->account_category->title = $title;
        $this->account_category->save();

        // Now check it!
        $ph_website_category = $this->phactory->get( 'website_categories', array( 'website_id' => self::WEBSITE_ID, 'category_id' => self::CATEGORY_ID ) );

        $this->assertEquals( $title, $ph_website_category->title );
    }

    /**
     * Hide
     */
    public function testHide() {
         // Set variables
        $category_ids = array( self::CATEGORY_ID );

        // Hide
        $this->account_category->hide( self::WEBSITE_ID, $category_ids );

        // Check IDS
        $ph_website_blocked_category = $this->phactory->get( 'website_blocked_category', array( 'website_id' => self::WEBSITE_ID, 'category_id' => self::CATEGORY_ID ) );

        $this->assertEquals( self::CATEGORY_ID, $ph_website_blocked_category->category_id );
    }

    /**
     * Unhide
     *
     * @depends testHide
     */
    public function testUnhide() {
         // Set variables
        $category_ids = array( self::CATEGORY_ID );

        // Hide
        $this->account_category->hide( self::WEBSITE_ID, $category_ids );

        // Unhide
        $this->account_category->unhide( self::WEBSITE_ID, $category_ids );

        // Check it
        $ph_website_blocked_category = $this->phactory->get( 'website_blocked_category', array( 'website_id' => self::WEBSITE_ID, 'category_id' => self::CATEGORY_ID ) );

        $this->assertEmpty( $ph_website_blocked_category );
    }

    /**
     * Test delete by account
     */
    public function testDeleteByAccount() {
        // Insert categories
        $this->phactory->create( 'website_categories' );

        // Delete by account
        $this->account_category->delete_by_account( self::WEBSITE_ID );

        // Make sure they exist
        $ph_website_category = $this->phactory->get( 'website_categories', array( 'website_id' => self::WEBSITE_ID ) );

        $this->assertEmpty( $ph_website_category );
    }

    /**
     * Test reorganizing categories
     *
     * @todo This needs to be more thorough
     */
    public function testReorganizeCategories() {
        // Stub
        $category = new Category();
        $category->get( self::CATEGORY_ID );

        // Create
        $this->phactory->create( 'website_categories' );

        // Reorganize
        $this->account_category->reorganize_categories( self::WEBSITE_ID, $category );

        // Get
        $ph_website_category = $this->phactory->get( 'website_categories', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEmpty( $ph_website_category );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_categories');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'title', 'wc.`date_updated`' );

        // Get
        $website_categories = $this->account_category->list_all( $dt->get_variables() );
        $website_category = current( $website_categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'AccountCategory', $website_categories );
        $this->assertEquals( self::TITLE, $website_category->title );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_categories');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'title', 'wc.`date_updated`' );

        // Get
        $count = $this->account_category->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        Category::$categories = null;
        Category::$categories_by_parent = null;
        $this->account_category = null;
    }
}
