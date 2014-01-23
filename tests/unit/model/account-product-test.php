<?php

require_once 'base-database-test.php';

class AccountProductTest extends BaseDatabaseTest {
    // Website Products
    const PRODUCT_ID = 7;
    const PRICE = 5;
    const STATUS = 1;
    const ACTIVE = 1;
    const SEQUENCE = 9;

    // Categories
    const CATEGORY_NAME = 'Swing Sets';

    // Products
    const INDUSTRY_ID = 1;
    const BRAND_ID = 3;
    const CATEGORY_ID = 23;
    const USER_ID_CREATED = 13;
    const SKU = 'ABCs123';
    const PUBLISH_DATE = '2014-01-22 00:00:00';
    const DATE_CREATED = '2014-01-01 00:00:00';

    // Brands
    const BRAND_NAME = 'Joes Shoes';

    // Industry
    const INDUSTRY_NAME = 'Shoes';

    // Product Images
    const IMAGE = 'goofy-shoe.png';

    /**
     * @var AccountProduct
     */
    private $account_product;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_product = new AccountProduct();

        // Define
        $this->phactory->define( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID, 'price' => self::PRICE, 'sequence' => self::SEQUENCE, 'status' => self::STATUS, 'active' => self::ACTIVE ) );
        $this->phactory->define( 'products', array( 'industry_id' => self::INDUSTRY_ID, 'brand_id' => self::BRAND_ID, 'category_id' => self::CATEGORY_ID, 'sku' => self::SKU, 'user_id_created' => self::USER_ID_CREATED, 'publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC, 'publish_date' => self::PUBLISH_DATE, 'date_created' => self::DATE_CREATED ) );
        $this->phactory->define( 'categories', array( 'name' => self::CATEGORY_NAME ) );
        $this->phactory->define( 'brands', array( 'name' => self::BRAND_NAME ) );
        $this->phactory->define( 'industries', array( 'name' => self::INDUSTRY_NAME ) );
        $this->phactory->define( 'product_images', array( 'image' => self::IMAGE ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $this->phactory->create( 'website_products' );

        // Get
        $this->account_product->get( self::PRODUCT_ID, self::WEBSITE_ID );

        $this->assertEquals( self::PRICE, $this->account_product->price );
    }

    /**
     * Test Get By Account
     */
    public function testGetByAccount() {
        // Insert
        $ph_category = $this->phactory->create( 'categories' );
        $ph_product = $this->phactory->create( 'products', array( 'category_id' => $ph_category->category_id ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Get
        $products = $this->account_product->get_by_account( self::WEBSITE_ID );
        $product = current( $products );

        $this->assertContainsOnlyInstancesOf( 'AccountProduct', $products );
        $this->assertEquals( self::SKU, $product->sku );
    }

    /**
     * Test Count
     */
    public function testCount() {
        // Insert
        $ph_product = $this->phactory->create( 'products' );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Get
        $count = $this->account_product->count( self::WEBSITE_ID );

        $this->assertGreaterThan( 0, $count );
    }

    /**
     * Test save
     */
    public function testSave() {
        // Create
        $this->phactory->create( 'website_products' );

        // Get
        $this->account_product->website_id = self::WEBSITE_ID;
        $this->account_product->product_id = self::PRODUCT_ID;
        $this->account_product->price = 82;
        $this->account_product->save();

        // Get phactory
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        $this->assertEquals( $this->account_product->price, $ph_website_product->price );
    }

    /**
     * Test Update Sequence
     */
    public function testUpdateSequence() {
        /// Create
        $this->phactory->create( 'website_products' );

        // Update sequence
        $this->account_product->update_sequence( self::WEBSITE_ID, array( self::PRODUCT_ID ) );

        // Get phactory
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );
        $expected_sequence = 0;

        $this->assertEquals( $expected_sequence, $ph_website_product->sequence );
    }

    /**
     * Test Search
     */
    public function testSearch() {
        // Insert
        $ph_industry = $this->phactory->create( 'industries' );
        $ph_brand = $this->phactory->create( 'brands' );
        $ph_category = $this->phactory->create( 'categories' );
        $ph_product = $this->phactory->create( 'products', array( 'category_id' => $ph_category->category_id, 'brand_id' => $ph_brand->brand_id, 'industry_id' => $ph_industry->industry_id ) );
        $this->phactory->create( 'product_images', array( 'product_id' => $ph_product->product_id ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Get
        $products = $this->account_product->search( self::WEBSITE_ID );
        $product = current( $products );

        $this->assertContainsOnlyInstancesOf( 'AccountProduct', $products );
        $this->assertEquals( self::SKU, $product->sku );
    }

    /**
     * Test Search Count
     */
    public function testSearchCount() {
        // Insert
        $ph_industry = $this->phactory->create( 'industries' );
        $ph_brand = $this->phactory->create( 'brands' );
        $ph_category = $this->phactory->create( 'categories' );
        $ph_product = $this->phactory->create( 'products', array( 'category_id' => $ph_category->category_id, 'brand_id' => $ph_brand->brand_id, 'industry_id' => $ph_industry->industry_id ) );
        $this->phactory->create( 'product_images', array( 'product_id' => $ph_product->product_id ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Get
        $count = $this->account_product->search_count( self::WEBSITE_ID );

        $this->assertGreaterThan( 0, $count );
    }

    /**
     * Test Adding bulk products
     */
    public function testAddBulk() {
        // Reset
        $this->phactory->recall();

        // Declare
        $skus = array( 'AA2010', 'AA2470' ); // 2470 has two products -- we should only get one

        // Create
        $this->phactory->create( 'products', array( 'sku' => 'AA2010' ) );
        $this->phactory->create( 'products', array( 'sku' => 'AA2470' ) );
        $this->phactory->create( 'products', array( 'sku' => 'AA2470' ) );

        // Add bulk
        $this->account_product->add_bulk( self::WEBSITE_ID, array( self::INDUSTRY_ID ), $skus );

        // Lets get the products
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID ) );
        $expected_count = 2;

        // Count products
        $this->assertCount( $expected_count, $website_products );
    }

    /**
     * Test Adding bulk products
     */
    public function testAddBulkCount() {
        // Reset
        $this->phactory->recall();

        // Declare
        $skus = array( 'AA2010', 'AA2470' ); // 2470 has two products -- we should only get one

        // Create
        $this->phactory->create( 'products', array( 'sku' => 'AA2010' ) );
        $this->phactory->create( 'products', array( 'sku' => 'AA2470' ) );
        $this->phactory->create( 'products', array( 'sku' => 'AA2470' ) );

        // Add bulk count
        $count = $this->account_product->add_bulk_count( self::WEBSITE_ID, array( self::INDUSTRY_ID ), $skus );
        $expected_count = 2;

        // Count products
        $this->assertEquals( $expected_count, $count );
    }

    /**
     * Test copy by account
     */
    public function testCopyByAccount() {
        // Reset
        $this->phactory->recall();

        // Declare
        $new_website_id = 55;

        // Create
        $this->phactory->create( 'website_products' );

        // Copy by account
        $this->account_product->copy_by_account( self::WEBSITE_ID, $new_website_id );

        // Get
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => $new_website_id ) );

        $this->assertEquals( self::ACTIVE, $ph_website_product->active );
    }

    /**
     * Test Getting an attribute item
     */
    public function testDeactivateByAccount() {
        // Reset
        $this->phactory->recall();

        // Create
        $this->phactory->create( 'website_products' );

        // Now, deactivate them all
        $this->account_product->deactivate_by_account( self::WEBSITE_ID );

        // Get
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID ) );
        $expected_active = 0;

        // Count products
        $this->assertEquals( $expected_active, $ph_website_product->active );
    }
//
//    /**
//     * Test Get Bulk SKUs To Be Added
//     */
//    public function testGetBulkSkusToBeAdded() {
//        // Declare Variables
//        $website_id = -7;
//        $industry_id = -5;
//        $industry_ids = array( $industry_id );
//        $publish_visibility = 'public';
//        $sku = 'A123B';
//        $skus = array( $sku );
//
//        // Insert
//        $this->phactory->insert( 'products', compact( 'industry_id', 'sku', 'publish_visibility' ), 'iss' );
//
//        $fetched_skus = $this->account_product->get_bulk_skus_to_be_added( $website_id, $industry_ids, $skus );
//
//        $this->assertEquals( $skus, $fetched_skus );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Test Get Bulk Already Existed SKUs
//     */
//    public function testGetBulkAlreadyExistedSkus() {
//        // Declare Variables
//        $website_id = -7;
//        $sku = 'A123B';
//        $skus = array( $sku );
//        $active = 1;
//
//        // Insert
//        $product_id = $this->phactory->insert( 'products', compact( 'sku' ), 's' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ), 'iii' );
//
//        $fetched_skus = $this->account_product->get_bulk_already_existed_skus( $website_id, $skus );
//
//        $this->assertEquals( $skus, $fetched_skus );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'product_id' ), 'i' );
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test Add Bulk All
//     *
//     * @depends testGetBulkSkusToBeAdded
//     * @depends testGetBulkAlreadyExistedSkus
//     * @depends testAddBulk
//     */
//    public function testAddBulkAll() {
//        // Declare Variables
//        $website_id = -17;
//        $industry_id = -5;
//        $industry_ids = array( $industry_id );
//        $sku_1 = '4010';
//        $sku_2 = '4470';
//        $sku_3 = '4570';
//        $skus = array( $sku_1, $sku_2, $sku_3 );
//
//        // Insert
//        $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_1, 'publish_visibility' => 'public' ), 'iiss' );
//        $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_2, 'publish_visibility' => 'public' ), 'iiss' );
//        $product_id = $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_3, 'publish_visibility' => 'public' ), 'iiss' );
//        $this->phactory->insert( 'website_products', compact( 'product_id', 'website_id' ), 'ii' );
//
//        // Add Bulk All
//        $this->account_product->add_bulk_all( $website_id, $industry_ids, $skus );
//
//        $fetched_skus = $this->phactory->get_col( "SELECT p.`sku` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`website_id` = $website_id ORDER BY `sku` ASC" );
//
//        $this->assertEquals( $skus, $fetched_skus );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test removing bulk items
//     *
//     * @depends testAddBulk
//     */
//    public function testRemoveBulk() {
//        // Declare variables
//        $account_id = -2;
//        $industry_id = -3;
//        $industry_ids = array( $industry_id );
//        $skus = array( '6010', '6470' ); // 2470 has two products -- we should only get one
//
//        // Clean up just in case
//        $this->phactory->delete( 'website_products', array( 'website_id' => $account_id ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//
//        $bulk_items_product_ids[] = $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => '6010', 'publish_visibility' => 'public' ), 'iiss' );
//        $bulk_items_product_ids[] = $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => '6470', 'publish_visibility' => 'public' ), 'iiss' );
//        $bulk_items_product_ids[] = $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => '6470', 'publish_visibility' => 'public' ), 'iiss' );
//
//        // Add bulk
//        $this->account_product->add_bulk( $account_id, $industry_ids, $skus );
//
//        // Remove bulk
//        $this->account_product->remove_bulk( $account_id, $bulk_items_product_ids );
//
//        // Lets get the products
//        $product_id_count = $this->phactory->get_var( 'SELECT COUNT( `product_id` ) FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $account_id );
//
//        // Count products
//        $this->assertEquals( 0, $product_id_count );
//    }
//
//    /**
//     * Test Adding bulk products by IDs
//     */
//    public function testAddBulkByIds() {
//        // Declare variables
//        $website_id = -3;
//        $industry_id = -4;
//
//        $product_ids[] = $this->phactory->insert( 'products', compact( 'industry_id' ), 'i' );
//        $product_ids[] = $this->phactory->insert( 'products', compact( 'industry_id' ), 'i' );
//        $product_ids[] = $this->phactory->insert( 'products', compact( 'industry_id' ), 'i' );
//
//        // Add Bulk
//        $this->account_product->add_bulk_by_ids( $website_id, $product_ids );
//
//        // Lets get the products
//        $count = $this->phactory->get_var( 'SELECT COUNT( `product_id` ) FROM `website_products` WHERE `active` = 1 AND `website_id` = ' . (int) $website_id );
//
//        // Count products
//        $this->assertEquals( 3, $count );
//
//        // Delete
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Test Adding bulk products by brand
//     */
//    public function testAddBulkByBrand() {
//        // Declare variables
//        $website_id = -5;
//        $industry_id = -12;
//        $brand_id = -3;
//        $publish_visibility = 'public';
//        $industry_ids = array( $industry_id );
//
//        // Clean up just in case
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//
//        // Insert
//        $this->phactory->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
//        $this->phactory->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
//        $this->phactory->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
//
//        $count = $this->account_product->add_bulk_by_brand( $website_id, $brand_id, $industry_ids );
//
//        // Count products
//        $this->assertEquals( 3, $count );
//
//        // Delete
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Test Adding bulk products by brand count
//     */
//    public function testAddBulkByBrandCount() {
//        // Declare variables
//        $website_id = -5;
//        $industry_id = -12;
//        $brand_id = -3;
//        $publish_visibility = 'public';
//        $industry_ids = array( $industry_id );
//
//        // Clean up just in case
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//
//        // Insert
//        $this->phactory->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
//        $this->phactory->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
//        $this->phactory->insert( 'products', compact( 'industry_id', 'brand_id', 'publish_visibility' ), 'iis' );
//
//        $count = $this->account_product->add_bulk_by_brand_count( $website_id, $brand_id, $industry_ids );
//
//        // Count products
//        $this->assertEquals( 3, $count );
//
//        // Delete
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Test Block By SKU
//     */
//    public function testBlockBySKU() {
//        // Declare Variables
//        $website_id = -19;
//        $industry_id = -5;
//        $industry_ids = array( $industry_id );
//        $sku_1 = '4010';
//        $sku_2 = '4470';
//        $sku_3 = '4570';
//        $skus = array( $sku_1, $sku_2, $sku_3 );
//
//        // Insert
//        $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_1, 'publish_visibility' => 'public' ), 'iiss' );
//        $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_2, 'publish_visibility' => 'public' ), 'iiss' );
//        $this->phactory->insert( 'products', array( 'industry_id' => $industry_id, 'sku' => $sku_3, 'publish_visibility' => 'public' ), 'iiss' );
//
//        // Block by SKU
//        $this->account_product->block_by_sku( $website_id, $industry_ids, $skus );
//
//        $count = $this->phactory->get_var( "SELECT COUNT(*) FROM `website_products` WHERE `website_id` = $website_id AND `blocked` = 1" );
//
//        // Count products
//        $this->assertEquals( 3, $count );
//
//        // Delete
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Test Unblocking Products
//     */
//    public function testUnblock() {
//        // Declare Variables
//        $website_id = -9;
//        $product_id = -5;
//        $product_ids = array( $product_id );
//        $blocked = 1;
//        $unblocked = 0;
//
//        // Insert
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'blocked' ) , 'iii' );
//
//        // Unblocked
//        $this->account_product->unblock( $website_id, $product_ids );
//
//        // Get product
//        $fetched_blocked = $this->phactory->get_var( "SELECT `blocked` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
//
//        $this->assertEquals( $unblocked, $fetched_blocked );
//
//        // Cleanup
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test Get Blocked
//     */
//    public function testGetBlocked() {
//        // Declare Variables
//        $website_id = -9;
//        $industry_id = -3;
//        $blocked = 1;
//
//        // Insert
//        $product_id = $this->phactory->insert( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'blocked' ) , 'iii' );
//
//        // Get Products
//        $products = $this->account_product->get_blocked( $website_id );
//
//        // Get product
//        $product = current( $products );
//        $this->assertEquals( $product_id, $product->id );
//
//        // Cleanup
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Test Remove Sale Items
//     */
//    public function testRemoveSaleItems() {
//        // Declare Variables
//        $website_id = -9;
//        $product_id = -5;
//        $on_sale = 1;
//        $not_on_sale = 0;
//
//        // Insert
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'on_sale' ) , 'iii' );
//
//        // Unblocked
//        $this->account_product->remove_sale_items( $website_id );
//
//        // Get product
//        $fetched_on_sale = $this->phactory->get_var( "SELECT `on_sale` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
//
//        $this->assertEquals( $not_on_sale, $fetched_on_sale );
//
//        // Cleanup
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test removing all products from accounts by an id
//     */
//    public function testDeleteByProduct() {
//        // Declare variables
//        $product_id = -5;
//        $account_id1 = -3;
//        $account_id2 = -2;
//
//        // Insert into accounts
//        $this->phactory->insert( 'website_products', array( 'product_id' => $product_id, 'website_id' => $account_id1, 'active' => 1 ), 'iii' );
//        $this->phactory->insert( 'website_products', array( 'product_id' => $product_id, 'website_id' => $account_id2, 'active' => 1 ), 'iii' );
//
//        // Delete them
//        $this->account_product->delete_by_product( $product_id );
//
//        // We should be able to get them
//        $active = $this->phactory->get_col( "SELECT `active` FROM `website_products` WHERE `product_id` = $product_id" );
//
//        $this->assertEquals( 2, count( $active ) );
//        $this->assertEquals( '0', $active[0] );
//
//        // Delete them
//        $this->phactory->delete( 'website_products', array( 'product_id' => $product_id ), 'i' );
//    }
//
//    /**
//     * Test Remove Discontinued
//     */
//    public function testRemoveDiscontinued() {
//        // Declare Variables
//        $website_id = -9;
//        $industry_id = -3;
//        $active = 1;
//        $inactive = 0;
//        $status = 'discontinued';
//
//        // Insert
//        $product_id = $this->phactory->insert( 'products', compact( 'industry_id', 'status' ), 'is' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ) , 'iii' );
//
//        // Unblocked
//        $this->account_product->remove_discontinued( $website_id );
//
//        // Get product
//        $fetched_active = $this->phactory->get_var( "SELECT `active` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
//
//        $this->assertEquals( $inactive, $fetched_active );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test AutocompleteAll
//     */
//    public function testAutocompleteAll() {
//        // Declare Variables
//        $website_id = -9;
//        $industry_id = -3;
//        $sku = 'Long Winded';
//        $query = 'Long';
//
//        // Insert
//        $this->phactory->insert( 'products', compact( 'industry_id', 'sku' ), 'is' );
//        $this->phactory->insert( 'website_industries', compact( 'website_id', 'industry_id' ) , 'ii' );
//
//        // Unblocked
//        $values = $this->account_product->autocomplete_all( $query, 'sku', $website_id );
//
//        // Get product
//        $this->assertTrue( FALSE !== stristr( $values[0]['name'], $query ) );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->delete( 'website_industries', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test Autocomplete By Account
//     */
//    public function testAutocompleteByAccount() {
//        // Declare Variables
//        $website_id = -9;
//        $industry_id = -3;
//        $sku = 'Short Winded';
//        $query = 'Short';
//        $publish_visibility = 'public';
//        $active = 1;
//
//        // Insert
//        $product_id = $this->phactory->insert( 'products', compact( 'industry_id', 'sku', 'publish_visibility' ), 'iss' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ), 'iii' );
//        $this->phactory->insert( 'website_industries', compact( 'website_id', 'industry_id' ) , 'ii' );
//
//        // Unblocked
//        $values = $this->account_product->autocomplete_by_account( $query, 'sku', $website_id );
//
//        // Get product
//        $this->assertTrue( FALSE !== stristr( $values[0]['name'], $query ) );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//        $this->phactory->delete( 'website_industries', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * List Products
//     */
//    public function testListProducts() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`', 'p.`name`' );
//
//        $products = $this->account_product->list_products( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( current( $products ) instanceof Product );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $products );
//    }
//
//    /**
//     * Count Products
//     */
//    public function testCountProducts() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`', 'p.`name`' );
//
//        $count = $this->account_product->count_products( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }
//
//    /**
//     * List Product Prices
//     */
//    public function testListProductPrices() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'p.`sku`', 'wp.`price`', 'wp.`price_note`', 'wp.`alternate_price_name`', 'wp.`sale_price`' );
//
//        $products = $this->account_product->list_product_prices( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( current( $products ) instanceof Product );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $products );
//    }
//
//    /**
//     * Count Product Prices
//     */
//    public function testCountProductPrices() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'p.`sku`', 'wp.`price`', 'wp.`price_note`', 'wp.`alternate_price_name`', 'wp.`sale_price`' );
//
//        $count = $this->account_product->count_product_prices( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }
//
//    /**
//     * Test Set Product Prices
//     */
//    public function testSetProductPrices() {
//        // Declare Variables
//        $website_id = -9;
//        $product_id = -3;
//        $active = 1;
//        $price = 2;
//        $prices = array (
//            $product_id => array(
//                'alternate_price' => 1
//                , 'price' => $price
//                , 'sale_price' => 3
//                , 'alternate_price_name' => 'Oboe'
//                , 'price_note' => 'Flute'
//            )
//        );
//
//        // Insert
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ), 'iii' );
//
//        // Unblocked
//        $this->account_product->set_product_prices( $website_id, $prices );
//
//        // Get price
//        $fetched_price = $this->phactory->get_var( "SELECT `price` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
//
//        $this->assertEquals( $price, $fetched_price );
//
//        // Cleanup
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test Multiply Product Prices By SKU
//     */
//    public function testMultiplyProductPricesBySku() {
//        // Declare Variables
//        $website_id = -9;
//        $industry_id = -3;
//        $sku = 'GF321';
//        $active = 1;
//        $price = 2;
//        $alternate_price = 100;
//        $price_multiplier = 3;
//        $sale_price_multiplier = 2;
//        $alternate_price_multiplier = 0;
//        $price_note = 'All inclusive';
//        $prices_array = array(
//            'price' => $price * $price_multiplier
//            , 'sale_price' => $price * $sale_price_multiplier
//            , 'alternate_price' => $alternate_price * $alternate_price_multiplier
//            , 'price_note' => $price_note
//        );
//
//        $prices = array (
//            compact( 'sku', 'price', 'price_note' )
//        );
//
//        // Insert
//        $product_id = $this->phactory->insert( 'products', compact( 'industry_id', 'sku' ), 'is' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id', 'alternate_price', 'active' ), 'iidi' );
//
//        // Unblocked
//        $this->account_product->multiply_product_prices_by_sku( $website_id, $prices, $price_multiplier, $sale_price_multiplier, $alternate_price_multiplier );
//
//        // Get price
//        $fetched_prices = $this->phactory->get_row( "SELECT `price`, `sale_price`, `alternate_price`, `price_note` FROM `website_products` WHERE `website_id` = $website_id AND `product_id` = $product_id", PDO::FETCH_ASSOC );
//
//        $this->assertEquals( $prices_array, $fetched_prices );
//
//        // Cleanup
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'products', compact( 'industry_id' ), 'i' );
//    }
//
//    /**
//     * Get discontinued website ids
//     */
//    public function testGetDiscontinuedWebsiteIds() {
//        // Make it possible to call this function
//        $class = new ReflectionClass('AccountProduct');
//        $method = $class->getMethod( 'get_discontinued_website_ids' );
//        $method->setAccessible(true);
//
//        // Declare variables
//        $website_id = -5;
//        $timestamp = '2012-01-01'; // needs to be 60 days prior
//        $status = 'discontinued';
//
//        // Create a product
//        $product_id = $this->phactory->insert( 'products', compact( 'website_id', 'status', 'timestamp' ), 'iss' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id' ), 'ii' );
//
//        $website_ids = $method->invoke( $this->account_product );
//
//        // Assert
//        $this->assertTrue( in_array( $website_id, $website_ids ) );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Get discontinued website ids
//     */
//    public function testRemoveAllDiscontinuedProducts() {
//        // Make it possible to call this function
//        $class = new ReflectionClass('AccountProduct');
//        $method = $class->getMethod( 'remove_all_discontinued_products' );
//        $method->setAccessible(true);
//
//        // Declare variables
//        $website_id = -5;
//        $timestamp = '2012-01-01'; // needs to be 60 days prior
//        $status = 'discontinued';
//
//        // Create a product
//        $product_id = $this->phactory->insert( 'products', compact( 'website_id', 'status', 'timestamp' ), 'iss' );
//        $this->phactory->insert( 'website_products', compact( 'website_id', 'product_id' ), 'ii' );
//
//        $method->invoke( $this->account_product );
//
//        // Assert
//        $website_ids = $this->phactory->get_col( "SELECT wp.`website_id` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`active` = 1 AND p.`status` = 'discontinued' AND p.`timestamp` < DATE_SUB( NOW(), INTERVAL 60 DAY )" );
//
//        $this->assertTrue( empty( $website_ids ) );
//
//        // Cleanup
//        $this->phactory->delete( 'products', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'website_products', compact( 'website_id' ), 'i' );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }
}
