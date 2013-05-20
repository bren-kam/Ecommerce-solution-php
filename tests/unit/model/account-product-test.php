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

        // Insert
        $category_id = $this->db->insert( 'categories', compact( 'name' ), 'i' );
        $product_id = $this->db->insert( 'products', compact( 'category_id', 'brand_id', 'user_id_created', 'publish_visibility' ), 'iiis' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'status', 'active' ), 'iiii' );

        // Get
        $products = $this->account_product->get_by_account( $website_id );

        $this->assertTrue( current( $products ) instanceof AccountProduct );

        // Delete
        $this->db->delete( 'products', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
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

        // Insert
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
     * Test Update Sequence
     */
    public function testUpdateSequence() {
        // Declare variables
        $website_id = -3;
        $product_ids = array( -7 );
        $sequence = 5;

        // Insert
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'sequence' ), 'iii' );

        $this->account_product->update_sequence( $website_id, $product_ids );

        // Get sequence
        $fetched_sequence = $this->db->get_var( "SELECT `sequence` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = " . (int) $product_ids[0] );

        $this->assertEquals( 0, $fetched_sequence );

        // Cleanup
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Search
     */
    public function testSearch() {
        // Declare variables
        $website_id = -5;
        $industry_name = 'Leagues';
        $brand_name = 'Justice';
        $name = 'Backlog';
        $user_id_created = 1;
        $status = $active = 1;
        $publish_visibility = 'public';
        $image = 'cool.png';
        $date_created = dt::now();

        // Insert
        $industry_id = $this->db->insert( 'industries', array( 'name' => $industry_name ), 's' );
        $brand_id = $this->db->insert( 'brands', array( 'name' => $brand_name ) , 's' );
        $category_id = $this->db->insert( 'categories', compact( 'name' ), 'i' );
        $product_id = $this->db->insert( 'products', compact( 'category_id', 'brand_id', 'industry_id', 'user_id_created', 'publish_visibility', 'date_created' ), 'iiiiss' );
        $this->db->insert( 'product_images', compact( 'product_id', 'image' ), 'is' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'status', 'active' ), 'iiii' );

        // Get
        $products = $this->account_product->search( $website_id );

        $this->assertTrue( current( $products ) instanceof AccountProduct );

        // Delete
        $this->db->delete( 'products', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'brands', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'industries', compact( 'industry_id' ), 'i' );
        $this->db->delete( 'product_images', compact( 'product_id' ), 'i' );
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test SearchCount
     */
    public function testSearchCount() {
        // Declare variables
        $website_id = -5;
        $industry_name = 'Leagues';
        $brand_name = 'Justice';
        $name = 'Backlog';
        $user_id_created = 1;
        $status = $active = 1;
        $publish_visibility = 'public';
        $image = 'cool.png';
        $date_created = dt::now();

        // Insert
        $industry_id = $this->db->insert( 'industries', array( 'name' => $industry_name ), 's' );
        $brand_id = $this->db->insert( 'brands', array( 'name' => $brand_name ) , 's' );
        $category_id = $this->db->insert( 'categories', compact( 'name' ), 'i' );
        $product_id = $this->db->insert( 'products', compact( 'category_id', 'brand_id', 'industry_id', 'user_id_created', 'publish_visibility', 'date_created' ), 'iiiiss' );
        $this->db->insert( 'product_images', compact( 'product_id', 'image' ), 'is' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'status', 'active' ), 'iiii' );

        // Get
        $count = $this->account_product->search_count( $website_id );

        $this->assertGreaterThan( 0, $count );

        // Delete
        $this->db->delete( 'products', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'brands', compact( 'brand_id' ), 'i' );
        $this->db->delete( 'industries', compact( 'industry_id' ), 'i' );
        $this->db->delete( 'product_images', compact( 'product_id' ), 'i' );
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Adding bulk products
     */
    public function testAddBulk() {
        // Declare variables
        $account_id = -2;
        $industry_id = -2;
        $industry_ids = array( -2 );

        // Clean up just in case
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );

        // Insert
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => 'AA2010', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => 'AA2470', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => 'AA2470', 'publish_visibility' => 'public' ), 'iiss' );
        $skus = array( 'AA2010', 'AA2470' ); // 2470 has two products -- we should only get one

        $this->account_product->add_bulk( $account_id, $industry_ids, $skus );

        // Lets get the products
        $count = $this->db->get_var( 'SELECT COUNT( `product_id` ) FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );

        // Count products
        $this->assertEquals( 2, $count );

        // Delete
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Adding bulk products
     */
    public function testAddBulkCount() {
        // Declare variables
        $website_id = -5;
        $industry_ids = array( -1 );

        $this->db->insert( 'products', array( 'industry_id' => -1, 'sku' => '3010', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => -1, 'sku' => '3470', 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => -1, 'sku' => '3470', 'publish_visibility' => 'public' ), 'iiss' );
        $skus = array( '3010', '3470' ); // 2470 has two products -- we should only get one

        $count = $this->account_product->add_bulk_count( $website_id, $industry_ids, $skus );

        // Count products
        $this->assertEquals( 2, $count );

        // Delete
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
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
     * Test Get Bulk SKUs To Be Added
     */
    public function testGetBulkSkusToBeAdded() {
        // Declare Variables
        $website_id = -7;
        $industry_id = -5;
        $industry_ids = array( $industry_id );
        $publish_visibility = 'public';
        $sku = 'A123B';
        $skus = array( $sku );

        // Insert
        $this->db->insert( 'products', compact( 'industry_id', 'sku', 'publish_visibility' ), 'iss' );

        $fetched_skus = $this->account_product->get_bulk_skus_to_be_added( $website_id, $industry_ids, $skus );

        $this->assertEquals( $skus, $fetched_skus );

        // Cleanup
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Get Bulk Already Existed SKUs
     */
    public function testGetBulkAlreadyExistedSkus() {
        // Declare Variables
        $website_id = -7;
        $sku = 'A123B';
        $skus = array( $sku );
        $active = 1;

        // Insert
        $product_id = $this->db->insert( 'products', compact( 'sku' ), 's' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ), 'iii' );

        $fetched_skus = $this->account_product->get_bulk_already_existed_skus( $website_id, $skus );

        $this->assertEquals( $skus, $fetched_skus );

        // Cleanup
        $this->db->delete( 'products', compact( 'product_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Add Bulk All
     *
     * @depends testGetBulkSkusToBeAdded
     * @depends testGetBulkAlreadyExistedSkus
     * @depends testAddBulk
     */
    public function testAddBulkAll() {
        // Declare Variables
        $website_id = -17;
        $industry_id = -5;
        $industry_ids = array( $industry_id );
        $sku_1 = '4010';
        $sku_2 = '4470';
        $sku_3 = '4570';
        $skus = array( $sku_1, $sku_2, $sku_3 );

        // Insert
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_1, 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_2, 'publish_visibility' => 'public' ), 'iiss' );
        $product_id = $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_3, 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'website_products', compact( 'product_id', 'website_id' ), 'ii' );

        // Add Bulk All
        $this->account_product->add_bulk_all( $website_id, $industry_ids, $skus );

        $fetched_skus = $this->db->get_col( "SELECT p.`sku` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`website_id` = $website_id ORDER BY `sku` ASC" );

        $this->assertEquals( $skus, $fetched_skus );

        // Cleanup
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test removing bulk items
     *
     * @depends testAddBulk
     */
    public function testRemoveBulk() {
        // Declare variables
        $account_id = -2;
        $industry_id = -3;
        $industry_ids = array( $industry_id );
        $skus = array( '6010', '6470' ); // 2470 has two products -- we should only get one

        // Clean up just in case
        $this->db->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );

        $bulk_items_product_ids[] = $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => '6010', 'publish_visibility' => 'public' ), 'iiss' );
        $bulk_items_product_ids[] = $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => '6470', 'publish_visibility' => 'public' ), 'iiss' );
        $bulk_items_product_ids[] = $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => '6470', 'publish_visibility' => 'public' ), 'iiss' );

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
     * Test Adding bulk products by IDs
     */
    public function testAddBulkByIds() {
        // Declare variables
        $website_id = -3;
        $industry_id = -4;

        $product_ids[] = $this->db->insert( 'products', compact( 'industry_id' ), 'i' );
        $product_ids[] = $this->db->insert( 'products', compact( 'industry_id' ), 'i' );
        $product_ids[] = $this->db->insert( 'products', compact( 'industry_id' ), 'i' );

        // Add Bulk
        $this->account_product->add_bulk_by_ids( $website_id, $product_ids );

        // Lets get the products
        $count = $this->db->get_var( 'SELECT COUNT( `product_id` ) FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $website_id );

        // Count products
        $this->assertEquals( 3, $count );

        // Delete
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Adding bulk products by brand
     */
    public function testAddBulkByBrand() {
        // Declare variables
        $website_id = -5;
        $industry_id = -12;
        $brand_id = -3;
        $publish_visibility = 'public';
        $industry_ids = array( $industry_id );

        // Clean up just in case
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );

        // Insert
        $this->db->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
        $this->db->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
        $this->db->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );

        $count = $this->account_product->add_bulk_by_brand( $website_id, $brand_id, $industry_ids );

        // Count products
        $this->assertEquals( 3, $count );

        // Delete
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Adding bulk products by brand count
     */
    public function testAddBulkByBrandCount() {
        // Declare variables
        $website_id = -5;
        $industry_id = -12;
        $brand_id = -3;
        $publish_visibility = 'public';
        $industry_ids = array( $industry_id );

        // Clean up just in case
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );

        // Insert
        $this->db->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
        $this->db->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
        $this->db->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );

        $count = $this->account_product->add_bulk_by_brand_count( $website_id, $brand_id, $industry_ids );

        // Count products
        $this->assertEquals( 3, $count );

        // Delete
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Block By SKU
     */
    public function testBlockBySKU() {
        // Declare Variables
        $website_id = -19;
        $industry_id = -5;
        $industry_ids = array( $industry_id );
        $sku_1 = '4010';
        $sku_2 = '4470';
        $sku_3 = '4570';
        $skus = array( $sku_1, $sku_2, $sku_3 );

        // Insert
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_1, 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_2, 'publish_visibility' => 'public' ), 'iiss' );
        $this->db->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_3, 'publish_visibility' => 'public' ), 'iiss' );

        // Block by SKU
        $this->account_product->block_by_sku( $website_id, $industry_ids, $skus );

        $count = $this->db->get_var( "SELECT COUNT(*) FROM `website_products` WHERE `website_id` = $website_id AND `blocked` = 1" );

        // Count products
        $this->assertEquals( 3, $count );

        // Delete
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Unblocking Products
     */
    public function testUnblock() {
        // Declare Variables
        $website_id = -9;
        $product_id = -5;
        $product_ids = array( $product_id );
        $blocked = 1;
        $unblocked = 0;

        // Insert
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'blocked' ) , 'iii' );

        // Unblocked
        $this->account_product->unblock( $website_id, $product_ids );

        // Get product
        $fetched_blocked = $this->db->get_var( "SELECT `blocked` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertEquals( $unblocked, $fetched_blocked );

        // Cleanup
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Get Blocked
     */
    public function testGetBlocked() {
        // Declare Variables
        $website_id = -9;
        $industry_id = -3;
        $blocked = 1;

        // Insert
        $product_id = $this->db->insert( 'products', compact( 'industry_id' ), 'i' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'blocked' ) , 'iii' );

        // Get Products
        $products = $this->account_product->get_blocked( $website_id );

        // Get product
        $product = current( $products );
        $this->assertEquals( $product_id, $product->id );

        // Cleanup
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Test Remove Sale Items
     */
    public function testRemoveSaleItems() {
        // Declare Variables
        $website_id = -9;
        $product_id = -5;
        $on_sale = 1;
        $not_on_sale = 0;

        // Insert
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'on_sale' ) , 'iii' );

        // Unblocked
        $this->account_product->remove_sale_items( $website_id );

        // Get product
        $fetched_on_sale = $this->db->get_var( "SELECT `on_sale` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertEquals( $not_on_sale, $fetched_on_sale );

        // Cleanup
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
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
     * Test Remove Discontinued
     */
    public function testRemoveDiscontinued() {
        // Declare Variables
        $website_id = -9;
        $industry_id = -3;
        $active = 1;
        $inactive = 0;
        $status = 'discontinued';

        // Insert
        $product_id = $this->db->insert( 'products', compact( 'industry_id', 'status' ), 'is' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ) , 'iii' );

        // Unblocked
        $this->account_product->remove_discontinued( $website_id );

        // Get product
        $fetched_active = $this->db->get_var( "SELECT `active` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertEquals( $inactive, $fetched_active );

        // Cleanup
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test AutocompleteAll
     */
    public function testAutocompleteAll() {
        // Declare Variables
        $website_id = -9;
        $industry_id = -3;
        $sku = 'Long Winded';
        $query = 'Long';

        // Insert
        $this->db->insert( 'products', compact( 'industry_id', 'sku' ), 'is' );
        $this->db->insert( 'website_industries', compact( 'website_id', 'industry_id' ) , 'ii' );

        // Unblocked
        $values = $this->account_product->autocomplete_all( $query, 'sku', $website_id );

        // Get product
        $this->assertTrue( FALSE !== stristr( $values[0]['name'], $query ) );

        // Cleanup
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
        $this->db->delete( 'website_industries', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Autocomplete By Account
     */
    public function testAutocompleteByAccount() {
        // Declare Variables
        $website_id = -9;
        $industry_id = -3;
        $sku = 'Short Winded';
        $query = 'Short';
        $publish_visibility = 'public';
        $active = 1;

        // Insert
        $product_id = $this->db->insert( 'products', compact( 'industry_id', 'sku', 'publish_visibility' ), 'iss' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ), 'iii' );
        $this->db->insert( 'website_industries', compact( 'website_id', 'industry_id' ) , 'ii' );

        // Unblocked
        $values = $this->account_product->autocomplete_by_account( $query, 'sku', $website_id );

        // Get product
        $this->assertTrue( FALSE !== stristr( $values[0]['name'], $query ) );

        // Cleanup
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
        $this->db->delete( 'website_industries', compact( 'website_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
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
     * Test Set Product Prices
     */
    public function testSetProductPrices() {
        // Declare Variables
        $website_id = -9;
        $product_id = -3;
        $active = 1;
        $price = 2;
        $prices = array (
            $product_id => array(
                'alternate_price' => 1
                , 'price' => $price
                , 'sale_price' => 3
                , 'alternate_price_name' => 'Oboe'
                , 'price_note' => 'Flute'
            )
        );

        // Insert
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ), 'iii' );

        // Unblocked
        $this->account_product->set_product_prices( $website_id, $prices );

        // Get price
        $fetched_price = $this->db->get_var( "SELECT `price` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertEquals( $price, $fetched_price );

        // Cleanup
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Multiply Product Prices By SKU
     */
    public function testMultiplyProductPricesBySku() {
        // Declare Variables
        $website_id = -9;
        $industry_id = -3;
        $sku = 'GF321';
        $active = 1;
        $price = 2;
        $alternate_price = 100;
        $price_multiplier = 3;
        $sale_price_multiplier = 2;
        $alternate_price_multiplier = 0;
        $price_note = 'All inclusive';
        $prices_array = array(
            'price' => $price * $price_multiplier
            , 'sale_price' => $price * $sale_price_multiplier
            , 'alternate_price' => $alternate_price
            , 'price_note' => $price_note
        );

        $prices = array (
            compact( 'sku', 'price', 'price_note' )
        );

        // Insert
        $product_id = $this->db->insert( 'products', compact( 'industry_id', 'sku' ), 'is' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'alternate_price', 'active' ), 'iidi' );

        // Unblocked
        $this->account_product->multiply_product_prices_by_sku( $website_id, $prices, $price_multiplier, $sale_price_multiplier, $alternate_price_multiplier );

        // Get price
        $fetched_prices = $this->db->get_row( "SELECT `price`, `sale_price`, `alternate_price`, `price_note` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id", PDO::FETCH_ASSOC );

        $this->assertEquals( $prices_array, $fetched_prices );

        // Cleanup
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
        $this->db->delete( 'products', compact( 'industry_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }
}
