<?php

require_once 'test/base-database-test.php';

class AccountProductTest extends BaseDatabaseTest {
    // Website Products
    const PRODUCT_ID = 7;
    const PRICE = 5;
    const STATUS = 1;
    const SEQUENCE = 9;
    const ALTERNATE_PRICE = 100;

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
        $this->phactory->define( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID, 'price' => self::PRICE, 'alternate_price' => self::ALTERNATE_PRICE, 'sequence' => self::SEQUENCE, 'blocked' => AccountProduct::UNBLOCKED, 'status' => self::STATUS, 'active' => AccountProduct::ACTIVE ) );
        $this->phactory->define( 'products', array( 'industry_id' => self::INDUSTRY_ID, 'brand_id' => self::BRAND_ID, 'category_id' => self::CATEGORY_ID, 'sku' => self::SKU, 'user_id_created' => self::USER_ID_CREATED, 'publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC, 'publish_date' => self::PUBLISH_DATE, 'date_created' => self::DATE_CREATED ) );
        $this->phactory->define( 'categories', array( 'name' => self::CATEGORY_NAME ) );
        $this->phactory->define( 'brands', array( 'name' => self::BRAND_NAME ) );
        $this->phactory->define( 'industries', array( 'name' => self::INDUSTRY_NAME ) );
        $this->phactory->define( 'product_images', array( 'image' => self::IMAGE ) );
        $this->phactory->define( 'website_industries', array( 'website_id' => self::WEBSITE_ID, 'industry_id' => self::INDUSTRY_ID ) );
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
        // Create
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
        // Declare
        $skus = array( 'AA2010', 'AA2470' ); // 2470 has two products -- we should only get one

        // Create
        $this->phactory->create( 'products', array( 'sku' => 'AA2010' ) );
        $this->phactory->create( 'products', array( 'sku' => 'AA2470' ) );
        $this->phactory->create( 'products', array( 'sku' => 'AA2470' ) );

        // Add bulk
        $added_skus_count = $this->account_product->add_bulk( self::WEBSITE_ID, array( self::INDUSTRY_ID ), $skus );

        // Lets get the products
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID ) );
        $expected_count = 2;

        // Count products
        $this->assertCount( $expected_count, $website_products );
        $this->assertEquals( $expected_count, $added_skus_count );
    }

    /**
     * Test Adding bulk products
     */
    public function testAddBulkCount() {
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
        // Declare
        $new_website_id = 55;

        // Create
        $this->phactory->create( 'website_products' );

        // Copy by account
        $this->account_product->copy_by_account( self::WEBSITE_ID, $new_website_id );

        // Get
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => $new_website_id ) );

        $this->assertEquals( AccountProduct::ACTIVE, $ph_website_product->active );
    }

    /**
     * Test Getting an attribute item
     */
    public function testDeactivateByAccount() {
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

    /**
     * Test Get Bulk SKUs To Be Added
     */
    public function testGetBulkSkusToBeAdded() {
        // Declare
        $skus = array( self::SKU );

        // Create
        $this->phactory->create( 'products' );

        $fetched_skus = $this->account_product->get_bulk_skus_to_be_added( self::WEBSITE_ID, array( self::INDUSTRY_ID ), $skus );

        $this->assertEquals( $skus, $fetched_skus );
    }

    /**
     * Test Get Bulk Already Existed SKUs
     */
    public function testGetBulkAlreadyExistedSkus() {
        // Declare
        $skus = array( self::SKU );

        // Create
        $ph_product = $this->phactory->create( 'products' );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        $fetched_skus = $this->account_product->get_bulk_already_existed_skus( self::WEBSITE_ID, $skus );

        $this->assertEquals( $skus, $fetched_skus );
    }

    /**
     * Test Add Bulk All
     *
     * @depends testGetBulkSkusToBeAdded
     * @depends testGetBulkAlreadyExistedSkus
     * @depends testAddBulk
     */
    public function testAddBulkAll() {
        // Declare
        $sku_1 = '4010';
        $sku_2 = '4470';
        $sku_3 = '4570';
        $expected_count = 2;
        $expected_quantity_already_existed = 1;
        $expected_not_added_skus = array();
        $skus = array( $sku_1, $sku_2, $sku_3 );

        // Insert
        $this->phactory->create( 'products', array( 'sku' => $sku_1 ) );
        $this->phactory->create( 'products', array( 'sku' => $sku_2 ) );
        $ph_product = $this->phactory->create( 'products', array( 'sku' => $sku_3 ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ), 'ii' );

        // Add Bulk All
        list ( $quantity, $quantity_already_existed, $not_added_skus ) = $this->account_product->add_bulk_all( self::WEBSITE_ID, array( self::INDUSTRY_ID ), $skus );

        $this->assertEquals( $expected_count, $quantity );
        $this->assertEquals( $expected_quantity_already_existed, $quantity_already_existed );
        $this->assertEquals( $expected_not_added_skus, $not_added_skus );

        $products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID ) );
        $expected_count = 3;

        $this->assertCount( $expected_count, $products );
    }

    /**
     * Test removing bulk items
     *
     * @depends testAddBulk
     */
    public function testRemoveBulk() {
        // Declare
        $sku_1 = '4010';
        $sku_2 = '4470';
        $sku_3 = '4570';

        $ph_product = $this->phactory->create( 'products', array( 'sku' => $sku_1 ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );
        $bulk_items_product_ids[] = $ph_product->product_id;

        $ph_product = $this->phactory->create( 'products', array( 'sku' => $sku_2 ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );
        $bulk_items_product_ids[] = $ph_product->product_id;

        $ph_product = $this->phactory->create( 'products', array( 'sku' => $sku_3 ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );
        $bulk_items_product_ids[] = $ph_product->product_id;

        // Remove bulk
        $this->account_product->remove_bulk( self::WEBSITE_ID, $bulk_items_product_ids );

        // Lets get the products
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'active' => AccountProduct::ACTIVE ) );
        $expected_count = 0;

        // Count products
        $this->assertCount( $expected_count, $website_products );
    }

    /**
     * Test Adding bulk products by IDs
     */
    public function testAddBulkByIds() {
        // Create
        $ph_product = $this->phactory->create( 'products' );
        $product_ids[] = $ph_product->product_id;

        $ph_product = $this->phactory->create( 'products' );
        $product_ids[] = $ph_product->product_id;

        $ph_product = $this->phactory->create( 'products' );
        $product_ids[] = $ph_product->product_id;

        // Add Bulk
        $this->account_product->add_bulk_by_ids( self::WEBSITE_ID, $product_ids );

        // Lets get the products
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'active' => AccountProduct::ACTIVE ) );

        // Count products
        $this->assertCount( count( $product_ids ), $website_products );
    }

    /**
     * Test Adding bulk products by brand
     */
    public function testAddBulkByBrand() {
        // Create
        $this->phactory->create( 'products' );
        $this->phactory->create( 'products' );
        $this->phactory->create( 'products' );

        $count = $this->account_product->add_bulk_by_brand( self::WEBSITE_ID, self::BRAND_ID, array( self::INDUSTRY_ID ) );
        $expected_count = 3;

        // Count products
        $this->assertEquals( $expected_count, $count );
    }

    /**
     * Test Adding bulk products by brand count
     */
    public function testAddBulkByBrandCount() {
        // Create
        $this->phactory->create( 'products' );
        $this->phactory->create( 'products' );
        $this->phactory->create( 'products' );

        $count = $this->account_product->add_bulk_by_brand_count( self::WEBSITE_ID, self::BRAND_ID, array( self::INDUSTRY_ID ) );
        $expected_count = 3;

        // Count products
        $this->assertEquals( $expected_count, $count );
    }

    /**
     * Test Block By SKU
     */
    public function testBlockBySKU() {
        // Declare
        $sku_1 = '4010';
        $sku_2 = '4470';
        $sku_3 = '4570';
        $skus = array( $sku_1, $sku_2, $sku_3 );

        // Create
        $this->phactory->create( 'products', array( 'sku' => $sku_1 ) );
        $this->phactory->create( 'products', array( 'sku' => $sku_2 ) );
        $this->phactory->create( 'products', array( 'sku' => $sku_3 ) );

        // Block by SKU
        $this->account_product->block_by_sku( self::WEBSITE_ID, array( self::INDUSTRY_ID ), $skus );

        // Lets get the products
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'blocked' => AccountProduct::BLOCKED ) );

        // Count products
        $this->assertCount( count( $skus ), $website_products );
    }

    /**
     * Test Unblocking Products
     */
    public function testUnblock() {
        // Create
        $this->phactory->create( 'website_products', array( 'blocked' => AccountProduct::BLOCKED ) );

        // Unblocked
        $this->account_product->unblock( self::WEBSITE_ID, array( self::PRODUCT_ID ) );

        // Get product
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'blocked' => AccountProduct::BLOCKED ) );
        $expected_count = 0;

        $this->assertCount( $expected_count, $website_products );
    }

    /**
     * Test Get Blocked
     */
    public function testGetBlocked() {
        // Create
        $ph_product = $this->phactory->create( 'products' );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id, 'blocked' => AccountProduct::BLOCKED ) );

        // Get Products
        $products = $this->account_product->get_blocked( self::WEBSITE_ID );
        $product = current( $products );

        $this->assertContainsOnlyInstancesOf( 'Product', $products );
        $this->assertEquals( self::SKU, $product->sku );
    }

    /**
     * Test Remove Sale Items
     */
    public function testRemoveSaleItems() {
        // Create
        $this->phactory->create( 'website_products', array( 'on_sale' => AccountProduct::ON_SALE ) );

        // Unblocked
        $this->account_product->remove_sale_items( self::WEBSITE_ID );

        // Get product
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'on_sale' => AccountProduct::ON_SALE ) );
        $expected_count = 0;

        $this->assertCount( $expected_count, $website_products );
    }

    /**
     * Test removing all products from accounts by an id
     */
    public function testDeleteByProduct() {
        // Declare variables
        $website_id_2 = 17;

        // Insert into accounts
        $this->phactory->create( 'website_products' );
        $this->phactory->create( 'website_products', array( 'website_id' => $website_id_2 ) );

        // Delete them
        $this->account_product->delete_by_product( self::PRODUCT_ID );

        // Get product
        $website_products = $this->phactory->getAll( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'on_sale' => AccountProduct::ON_SALE ) );
        $expected_count = 0;

        $this->assertCount( $expected_count, $website_products );
    }

    /**
     * Test Remove Discontinued
     */
    public function testRemoveDiscontinued() {
        // Declare
        $status = 'discontinued';

        // Insert
        $ph_product = $this->phactory->create( 'products', compact( 'status' ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Unblocked
        $this->account_product->remove_discontinued( self::WEBSITE_ID );

        // Get product
        $website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => $ph_product->product_id ) );

        $this->assertEquals( AccountProduct::INACTIVE, $website_product->active );
    }

    /**
     * Test AutocompleteAll
     */
    public function testAutocompleteAll() {
        // Declare Variables
        $sku = 'Long Winded';
        $query = 'Long';

        // Insert
        $this->phactory->create( 'products', compact( 'sku' ) );
        $this->phactory->create( 'website_industries' );

        // Unblocked
        $values = $this->account_product->autocomplete_all( $query, 'sku', self::WEBSITE_ID );

        // Get product
        $this->assertEquals( $sku, $values[0]['name'] );
    }

    /**
     * Test Autocomplete By Account
     */
    public function testAutocompleteByAccount() {
        // Declare Variables
        $sku = 'Long Winded';
        $query = 'Long';

        // Insert
        $ph_product = $this->phactory->create( 'products', compact( 'sku' ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );
        $this->phactory->create( 'website_industries' );

        // Unblocked
        $values = $this->account_product->autocomplete_by_account( $query, 'sku', self::WEBSITE_ID );

        // Get product
        $this->assertEquals( $sku, $values[0]['name'] );
    }

    /**
     * List Products
     */
    public function testListProducts() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $ph_brand = $this->phactory->create('brands');
        $ph_product = $this->phactory->create( 'products', array( 'brand_id' => $ph_brand->brand_id ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`', 'p.`name`' );

        $products = $this->account_product->list_products( $dt->get_variables() );
        $product = current( $products );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'Product', $products );
        $this->assertEquals( self::SKU, $product->sku );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Count Products
     */
    public function testCountProducts() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $ph_brand = $this->phactory->create('brands');
        $ph_product = $this->phactory->create( 'products', array( 'brand_id' => $ph_brand->brand_id ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
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
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $ph_product = $this->phactory->create( 'products' );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'p.`sku`', 'wp.`price`', 'wp.`price_note`', 'wp.`alternate_price_name`', 'wp.`sale_price`' );

        $products = $this->account_product->list_product_prices( $dt->get_variables() );
        $product = current( $products );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'Product', $products );
        $this->assertEquals( self::SKU, $product->sku );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Count Product Prices
     */
    public function testCountProductPrices() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $ph_product = $this->phactory->create( 'products' );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
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
        $price = 2;
        $prices = array (
            self::PRODUCT_ID => array(
                'alternate_price' => 1
                , 'price' => $price
                , 'sale_price' => 3
                , 'alternate_price_name' => 'Oboe'
                , 'price_note' => 'Flute'
            )
        );

        // Create
        $this->phactory->create( 'website_products' );

        // Unblocked
        $this->account_product->set_product_prices( self::WEBSITE_ID, $prices );

        // Get price
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        $this->assertEquals( $price, $ph_website_product->price );
    }

    /**
     * Test Multiply Product Prices By SKU
     */
    public function testMultiplyProductPricesBySku() {
        // Declare Variables
        $sku = 'GF321';
        $price = 2;
        $price_multiplier = 3;
        $sale_price_multiplier = 2;
        $alternate_price_multiplier = 0;
        $price_note = 'All inclusive';

        $prices = array (
            compact( 'sku', 'price', 'price_note' )
        );

        // Create
        $ph_product = $this->phactory->create( 'products', compact( 'sku' ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Multiply
        $this->account_product->multiply_product_prices_by_sku( self::WEBSITE_ID, $prices, $price_multiplier, $sale_price_multiplier, $alternate_price_multiplier );

        // Get price
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => $ph_product->product_id ) );

        $this->assertEquals( $price * $price_multiplier, $ph_website_product->price );
    }

    /**
     * Get discontinued website ids
     */
    public function testGetDiscontinuedWebsiteIds() {
        // Make it possible to call this function
        $class = new ReflectionClass('AccountProduct');
        $method = $class->getMethod( 'get_discontinued_website_ids' );
        $method->setAccessible(true);

        // Declare
        $status = 'discontinued';

        // Create
        $ph_product = $this->phactory->create( 'products', compact( 'status' ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        $website_ids = $method->invoke( $this->account_product );

        // Assert
        $this->assertEquals( array( self::WEBSITE_ID ), $website_ids );
    }

    /**
     * Get discontinued website ids
     */
    public function testRemoveAllDiscontinuedProducts() {
        // Make it possible to call this function
        $class = new ReflectionClass('AccountProduct');
        $method = $class->getMethod( 'remove_all_discontinued_products' );
        $method->setAccessible(true);

        // Declare variables
        $status = 'discontinued';

        // Create
        $ph_product = $this->phactory->create( 'products', compact( 'status' ) );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // remove all discontinued products
        $method->invoke( $this->account_product );

        // Assert
        $website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => $ph_product->product_id ) );

        $this->assertEquals( AccountProduct::INACTIVE, $website_product->active );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product = null;
    }

    /**
     * Test Reset Price By Account
     */
    public function testResetPriceByAccount() {
        // Create
        $this->phactory->create( 'website_products' );

        // Now, deactivate them all
        $this->account_product->reset_price_by_account( self::WEBSITE_ID );

        // Get
        $ph_website_product = $this->phactory->get( 'website_products', array( 'website_id' => self::WEBSITE_ID ) );

        $expected_price = 0;

        // Check price
        $this->assertEquals( $expected_price, $ph_website_product->price );
        $this->assertEquals( $expected_price, $ph_website_product->alternate_price );
        $this->assertEquals( $expected_price, $ph_website_product->sale_price );
    }

}
