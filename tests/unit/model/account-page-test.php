<?php

require_once 'base-database-test.php';

class AccountPageTest extends BaseDatabaseTest {
    /**
     * @var AccountPage
     */
    private $account_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page = new AccountPage();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $slug = 'currents-offers';

        // Create
        $website_page_id = $this->db->insert( 'website_pages', compact( 'website_id', 'slug' ), 'is' );

        // Get
        $this->account_page->get( $website_page_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $slug, $this->account_page->slug );

        // Clean up
        $this->db->delete( 'website_pages', compact( 'website_id' ), 'i' );
    }

    /**
     * Get By Slug
     */
    public function testGetBySlug() {
        // Set variables
        $website_id = -7;
        $slug = 'currents-offers';
        $title = 'The great and powerful Oz';

        // Create
        $this->db->insert( 'website_pages', compact( 'website_id', 'slug', 'title' ), 'is' );

        // Get
        $this->account_page->get_by_slug( $website_id, $slug );

        // Make sure we grabbed the right one
        $this->assertEquals( $title, $this->account_page->title );

        // Clean up
        $this->db->delete( 'website_pages', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Getting all attributes
     */
    public function testGetAll() {
        // Declare variables
        $account_id = 96; // Test account

        $account_pages = $this->account_page->get_all( $account_id );

        $this->assertTrue( isset( $account_pages[0]['slug'] ) );
    }

    /**
     * Test Getting Product IDs
     */
    public function testGetProductIds() {
        // Declare Variables
        $this->account_page->id = $website_page_id = -7;
        $product_id = -5;
        $product_ids = array( $product_id );

        // Insert
        $this->db->insert( 'website_page_product', compact( 'website_page_id', 'product_id' ), 'ii' );

        // Get
        $fetched_product_ids = $this->account_page->get_product_ids();

        $this->assertEquals( $product_ids, $fetched_product_ids );

        // Clean up
        $this->db->delete( 'website_page_product', compact( 'website_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->account_page->website_id = -3;
        $this->account_page->slug = 'road-runner';
        $this->account_page->title = 'Road Runner';
        $this->account_page->content = 'Watch out for the super fast road runner!';
        $this->account_page->create();

        $this->assertTrue( !is_null( $this->account_page->id ) );

        // Make sure it's in the database
        $slug = $this->db->get_var( 'SELECT `slug` FROM `website_pages` WHERE `website_id` = ' . (int) $this->account_page->website_id );

        $this->assertEquals( $this->account_page->slug, $slug );

        // Delete the attribute
        $this->db->delete( 'website_pages', array( 'website_page_id' => $this->account_page->id ), 'i' );
    }

    /**
     * Test Add Products
     */
    public function testAddProducts() {
        // Declare Variables
        $this->account_page->id = $website_page_id = -7;
        $product_ids = array( -1, -2, -3 );

        // Add products
        $this->account_page->add_products( $product_ids );

        // Get ids
        $fetched_product_ids = $this->db->get_col( "SELECT `product_id` FROM `website_page_product` WHERE `website_page_id` = $website_page_id ORDER BY `product_id` DESC" );

        $this->assertEquals( $product_ids, $fetched_product_ids );

        // Cleanup
        $this->db->delete( 'website_page_product', compact( 'website_page_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Set variables
        $website_id = -7;
        $slug = 'wizard-of-oz';

        // Create
        $website_page_id = $this->db->insert( 'website_pages', compact( 'website_id' ), 'i' );

        // Get
        $this->account_page->get( $website_page_id, $website_id );
        $this->account_page->slug = $slug;
        $this->account_page->save();

        // Now check it!
        $retrieved_slug = $this->db->get_var( "SELECT `slug` FROM `website_pages` WHERE `website_page_id` = $website_page_id" );

        $this->assertEquals( $retrieved_slug, $slug );

        // Clean up
        $this->db->delete( 'website_pages', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Copy by Account
     */
    public function testCopyByAccount() {
        // Declare variables
        $template_account_id = 96;
        $account_id = -5;

        // Delete before
        $this->db->delete( 'website_pages', array( 'website_id' => $account_id ) , 'i' );

        // Do the copying
        $this->account_page->copy_by_account( $template_account_id, $account_id );

        // Get account page ids
        $account_page_ids = $this->db->get_col( "SELECT `website_page_id` FROM `website_pages` WHERE `website_id` = $account_id" );

        $this->assertGreaterThan( 5, count( $account_page_ids ) );

        // Delete
        $this->db->delete( 'website_pages', array( 'website_id' => $account_id ) , 'i' );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Set variables
        $website_id = -7;
        $slug = 'currents-offers';

        // Create
        $website_page_id = $this->db->insert( 'website_pages', compact( 'website_id', 'slug' ), 'is' );

        // Get
        $this->account_page->get( $website_page_id, $website_id );

        // Remove/Delete
        $this->account_page->remove();

        $retrieved_slug = $this->db->get_var( "SELECT `slug` FROM `website_pages` WHERE `website_page_id` = $website_page_id" );

        $this->assertFalse( $retrieved_slug );
    }

    /**
     * Test Delete Products
     */
    public function testDeleteProducts() {
        // Declare Variables
        $this->account_page->id = $website_page_id = -7;
        $product_id = -5;

        // Insert
        $this->db->insert( 'website_page_product', compact( 'website_page_id', 'product_id' ), 'ii' );

        // Delete
        $this->account_page->delete_products();

        // Get
        $fetched_product_id = $this->db->get_var( "SELECT `product_id` FROM `website_page_product` WHERE `website_page_id` = $website_page_id" );

        $this->assertFalse( $fetched_product_id );
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
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`title`');

        $account_pages = $this->account_page->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $account_pages ) instanceof AccountPage );

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
        $dt->order_by( '`title`', '`status`', '`date_updated`' );

        $count = $this->account_page->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_page = null;
    }
}
