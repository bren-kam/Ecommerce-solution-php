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
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $website_id = -5;
        $product_id = -7;
        $price = 5;

        // Insert
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'price' ), 'iid' );

        // Get
        $this->account_product->get( $product_id, $website_id );

        $this->assertEquals( $price, $this->account_product->price );

        // Delete
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Get By Account
     */
    public function testGetByAccount() {
        // Declare variables
        $website_id = -5;
        $brand_id = -3;
        $name = 'Backlog';
        $user_id_created = 1;
        $status = $active = 1;
        $publish_visibility = 'public';

        // Insert an email list
        $product_id = $this->db->insert( 'products', compact( 'brand_id', 'user_id_created', 'publish_visibility' ), 'iis' );
        $category_id = $this->db->insert( 'categories', compact( 'name' ), 'i' );
        $this->db->insert( 'product_categories', compact( 'product_id', 'category_id' ), 'ii' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'status', 'active' ), 'iiii' );

        // Get
        $products = $this->account_product->get_by_account( $website_id );

        $this->assertTrue( current( $products ) instanceof AccountProduct );

        // Delete
        $this->db->delete( 'products', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'product_categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Count
     */
    public function testCount() {
        // Declare variables
        $website_id = -5;
        $brand_id = -3;
        $user_id_created = 1;
        $status = $active = 1;
        $publish_visibility = 'public';
        $publish_date = dt::now();

        // Insert an email list
        $product_id = $this->db->insert( 'products', compact( 'brand_id', 'user_id_created', 'publish_visibility', 'publish_date' ), 'iiss' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'status', 'active' ), 'iiii' );

        // Get
        $count = $this->account_product->count( $website_id );

        $this->assertGreaterThan( 0, $count );

        // Delete
        $this->db->delete( 'products', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Test save
     *
     * @depends testGet
     */
    public function testSave() {
        // Declare variables
        $website_id = -3;
        $product_id = -7;
        $price = 6;

        // Insert
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id' ), 'ii' );

        // Get
        $this->account_product->get( $product_id, $website_id );

        // Save
        $this->account_product->price = $price;
        $this->account_product->save();

        // Make sure it's in the database
        $fetched_price = $this->db->get_var( 'SELECT `price` FROM `website_products` WHERE `website_id` = ' . (int) $website_id . ' AND `product_id` = ' . (int) $product_id );

        $this->assertEquals( $price, $fetched_price );

        // Delete
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
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
     * List Products
     */
    public function testListProducts() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`', 'p.`name`' );

        $products = $this->account_product->list_products( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $products ) instanceof Product );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Count Products
     */
    public function testCountProducts() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`', 'p.`name`' );

        $count = $this->account_product->count_products( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * List Product Prices
     */
    public function testListProductPrices() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'p.`sku`', 'wp.`price`', 'wp.`price_note`', 'wp.`alternate_price_name`', 'wp.`sale_price`' );

        $products = $this->account_product->list_product_prices( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $products ) instanceof Product );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Count Product Prices
     */
    public function testCountProductPrices() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'p.`sku`', 'wp.`price`', 'wp.`price_note`', 'wp.`alternate_price_name`', 'wp.`sale_price`' );

        $count = $this->account_product->count_product_prices( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }
}
