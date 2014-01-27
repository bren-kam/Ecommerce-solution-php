<?php

require_once 'test/base-database-test.php';

class AccountPageTest extends BaseDatabaseTest {
    const SLUG = 'page-slug';
    const TITLE = 'Home-fry Skillet';
    const WEBSITE_PAGE_ID = 3;
    const PRODUCT_ID = 5;

    /**
     * @var AccountPage
     */
    private $account_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page = new AccountPage();

        // Define
        $this->phactory->define( 'website_pages', array( 'website_id' => self::WEBSITE_ID, 'slug' => self::SLUG, 'title' => self::TITLE ) );
        $this->phactory->define( 'website_page_product', array( 'website_page_id' => self::WEBSITE_PAGE_ID, 'product_id' => self::PRODUCT_ID ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_page = $this->phactory->create( 'website_pages' );

        // Get
        $this->account_page->get( $ph_website_page->website_page_id, self::WEBSITE_ID );

        // Make sure we grabbed the right one
        $this->assertEquals( self::SLUG, $this->account_page->slug );
    }

    /**
     * Get By Slug
     */
    public function testGetBySlug() {
        // Create
        $this->phactory->create( 'website_pages' );

        // Get
        $this->account_page->get_by_slug( self::WEBSITE_ID, self::SLUG );

        // Make sure we grabbed the right one
        $this->assertEquals( self::TITLE, $this->account_page->title );
    }

    /**
     * Test Getting all attributes
     */
    public function testGetAll() {
        // Create
        $this->phactory->create( 'website_pages' );

        // Get
        $account_pages = $this->account_page->get_all( self::WEBSITE_ID );
        $account_page = current( $account_pages );

        $this->assertEquals( self::SLUG, $account_page['slug'] );
    }

    /**
     * Test Getting Product IDs
     */
    public function testGetProductIds() {
        // Insert
        $this->phactory->create( 'website_page_product' );

        // Get
        $this->account_page->id = self::WEBSITE_PAGE_ID;
        $fetched_product_ids = $this->account_page->get_product_ids();

        $this->assertEquals( array( self::PRODUCT_ID ), $fetched_product_ids );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->account_page->website_id = -3;
        $this->account_page->slug = self::SLUG;
        $this->account_page->title = self::TITLE;
        $this->account_page->create();

        $this->assertNotNull( $this->account_page->id );

        // Make sure it's in the database
        $ph_website_page = $this->phactory->get( 'website_pages', array( 'website_page_id' => $this->account_page->id ) );

        $this->assertEquals( self::SLUG, $ph_website_page->slug );
    }

    /**
     * Test Add Products
     */
    public function testAddProducts() {
        // Add products
        $this->account_page->id = self::WEBSITE_PAGE_ID;
        $this->account_page->add_products( array( self::PRODUCT_ID ) );

        // Get ids
        $ph_website_page_product = $this->phactory->get( 'website_page_product', array( 'product_id' => self::PRODUCT_ID ) );

        $this->assertEquals( self::PRODUCT_ID, $ph_website_page_product->product_id );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_website_page = $this->phactory->create( 'website_pages' );

        // Get
        $this->account_page->id = $ph_website_page->website_page_id;
        $this->account_page->slug = 'second-slug';
        $this->account_page->save();

        // Now check it!
        $ph_website_page = $this->phactory->get( 'website_pages', array( 'website_page_id' => $ph_website_page->website_page_id ) );

        $this->assertEquals( $this->account_page->slug, $ph_website_page->slug );
    }

    /**
     * Test Copy by Account
     */
    public function testCopyByAccount() {
        // Declare
        $new_website_id = 55;

        // Create
        $this->phactory->create( 'website_pages' );

        // Do the copying
        $this->account_page->copy_by_account( self::WEBSITE_ID, $new_website_id );

        // Get account page ids
        $ph_website_page = $this->phactory->get( 'website_pages', array( 'website_id' => $new_website_id ) );

        $this->assertEquals( self::SLUG, $ph_website_page->slug );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Insert
        $ph_website_page = $this->phactory->create( 'website_pages' );

        // Delete it!
        $this->account_page->id = $ph_website_page->website_page_id;
        $this->account_page->remove();

        // Shouldn't exist
        $ph_website_page = $this->phactory->get( 'website_pages', array( 'website_page_id' => $ph_website_page->website_page_id ) );

        $this->assertNull( $ph_website_page );
    }

    /**
     * Test Delete Products
     */
    public function testDeleteProducts() {
        // Insert
        $this->phactory->create( 'website_page_product' );

        // Delete
        $this->account_page->id = self::WEBSITE_PAGE_ID;
        $this->account_page->delete_products();

        // Get
        $ph_website_page_product = $this->phactory->get( 'website_page_product', array( 'website_page_id' => self::WEBSITE_PAGE_ID ) );

        $this->assertNull( $ph_website_page_product );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Insert
        $this->phactory->create( 'website_pages' );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`title`');

        $account_pages = $this->account_page->list_all( $dt->get_variables() );
        $account_page = current( $account_pages );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'AccountPage', $account_pages );
        $this->assertEquals( self::SLUG, $account_page->slug );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Insert
        $this->phactory->create( 'website_pages' );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
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
