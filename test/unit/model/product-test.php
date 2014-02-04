<?php

require_once 'test/base-database-test.php';

class ProductTest extends BaseDatabaseTest {
    /**
     * @var Product
     */
    private $product;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->product = new Product();
    }

    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Test Getting a ticket
//     */
//    public function testGet() {
//        // Declare Variables
//        $product_id = 36385;
//        $name = 'ZZZZZZZZZ Test';
//
//        // Get
//        $this->product->get( $product_id );
//
//        $this->assertEquals( $this->product->name, $name );
//    }
//
//    /**
//     * Get By Sku
//     */
//    public function testGetBySku() {
//        // Declare Variables
//        $sku = 'mess-with-me';
//
//        // Create
//        $product_id = $this->phactory->insert( 'products', compact( 'sku' ), 's' );
//
//        // Get
//        $this->product->get_by_sku( $sku );
//
//        $this->assertEquals( $this->product->sku, $sku );
//
//        // Clean Up
//        $this->phactory->delete( 'products', compact( 'product_id' ), 'i' );
//    }
//
//    /**
//     * Get By Ids
//     */
//    public function testGetByIds() {
//        // Declare Variables
//        $website_id = -1;
//
//        // Create
//        $product_id = $this->phactory->insert( 'products', compact( 'website_id' ), 'i' );
//        $product_id2 = $this->phactory->insert( 'products', compact( 'website_id' ), 'i' );
//        $product_ids = array( $product_id, $product_id2 );
//
//        // Get
//        $products = $this->product->get_by_ids( $product_ids );
//
//        $this->assertTrue( current( $products ) instanceof Product );
//
//        // Clean Up
//        $this->phactory->delete( 'products', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test Getting images
//     *
//     * @depends testGet
//     */
//    public function testGetImages() {
//        // Declare variables
//        $product_id = 4;
//
//        // Load Product
//        $this->product->get( $product_id );
//
//        // Get images
//        $images = $this->product->get_images();
//
//        $this->assertEquals( count( $images ), 3 );
//        $this->assertTrue( is_string( $images[0] ) );
//    }
//
//    /**
//     * Test creating a company
//     *
//     * @depends testGet
//     */
//    public function testCreate() {
//        // Declare variables
//        $website_id = 0;
//        $user_id_created = 1;
//        $publish_visibility = 'deleted';
//
//        // Create
//        $this->product->website_id = $website_id;
//        $this->product->user_id_created = $user_id_created;
//        $this->product->create();
//
//        // Make sure it's in the database
//        $this->product->get( $this->product->id );
//
//        $this->assertEquals( $publish_visibility, $this->product->publish_visibility );
//
//        // Delete the company
//        $this->phactory->delete( 'products', array( 'product_id' => $this->product->id ), 'i' );
//    }
//
//    /**
//     * Test Adding Images
//     *
//     * @depends testGet
//     */
//    public function testAddImages() {
//        // Declare variables
//        $product_id = 1;
//        $images = array( 'test.png', 'test1.gif' );
//
//        // Delete any images from before hand
//        $this->phactory->delete( 'product_images', array( 'product_id' => $product_id ) , 'i' );
//
//        // Get product
//        $this->product->get( $product_id );
//
//        // Add images
//        $this->product->add_images( $images );
//
//        // See if they are there
//        $fetched_images = $this->phactory->get_col( "SELECT `image` FROM `product_images` WHERE `product_id` = $product_id ORDER BY `image` ASC" );
//
//        $this->assertEquals( $images, $fetched_images );
//
//        // Delete any images from before hand
//        $this->phactory->delete( 'product_images', array( 'product_id' => $product_id ) , 'i' );
//    }
//
//    /**
//     * Test updating a product
//     *
//     * @depends testGet
//     */
//    public function testSave() {
//        // Declare variables
//        $product_id = 36385;
//
//        $this->phactory->update( 'products', array( 'publish_visibility' => 'deleted' ), array( 'product_id' => $product_id ), 's', 'i' );
//
//        $this->product->get( $product_id );
//
//        // Update test
//        $this->product->publish_visibility = 'public';
//        $this->product->save();
//
//        $publish_visibility = $this->phactory->get_var( "SELECT `publish_visibility` FROM `products` WHERE `product_id` = $product_id" );
//
//        $this->assertEquals( $publish_visibility, 'public' );
//    }
//
//    /**
//     * Test Delete Images
//     *
//     * @depends testGet
//     * @depends testAddImages
//     */
//    public function testDeleteImages() {
//        // Declare Variables
//        $product_id = 1;
//        $images = array( 'no.jpg', 'yes.png' );
//
//        // Get product
//        $this->product->get( $product_id );
//
//        // Add category
//        $this->product->add_images( $images );
//
//        // Delete images
//        $this->product->delete_images();
//
//        // Make sure there are no images
//        $images = $this->phactory->get_col( "SELECT `image` FROM `product_images` WHERE `product_id` = $product_id" );
//
//        $this->assertTrue( 0 == count( $images ) );
//    }
//
//    /**
//     * Test Cloning a product
//     */
//    public function testCloneProduct() {
//        $this->product->clone_product( 36385, 1 );
//
//        $name = $this->phactory->get_var( 'SELECT `name` FROM `products` WHERE `product_id` = ' . (int) $this->product->id );
//
//        $this->assertEquals( $name, 'ZZZZZZZZZ Test (Clone)' );
//
//        $this->phactory->delete( 'products', array( 'product_id' => $this->product->id ), 'i' );
//    }
//
//    /**
//     * Test listing all products
//     */
//    public function testListAll() {
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
//        $dt->order_by( 'p.`name`', 'u.`contact_name`', 'u2.`contact_name`', 'b.`name`', 'p.`sku`', 'c.`name`' );
//
//        $products = $this->product->list_all( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( $products[0] instanceof Product );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $products );
//    }
//
//    /**
//     * Test counting the products
//     */
//    public function testCountAll() {
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
//        $dt->order_by( 'a.`name`', 'e.`contact_name`', 'f.`contact_name`', 'd.`name`', 'a.`sku`', 'a.`status`' );
//
//        $count = $this->product->count_all( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }
//
//    /**
//     * List Custom Products
//     */
//    public function testListCustomProducts() {
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
//        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'c.`name`', 'p.`status`', 'p.`publish_date`' );
//
//        $products = $this->product->list_custom_products( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( $products[0] instanceof Product );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $products );
//    }
//
//    /**
//     * Count Custom Products
//     */
//    public function testCountCustomProducts() {
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
//        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'c.`name`', 'p.`status`', 'p.`publish_date`' );
//
//        $count = $this->product->count_custom_products( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }
//
//    /**
//     * Test Autocomplete
//     */
//    public function testAutocomplete() {
//        $products = $this->product->autocomplete( '1111', 'p.`sku`', 'sku', '' );
//
//        $this->assertTrue( isset( $products[0]['sku'] ) );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->product = null;
    }
}
