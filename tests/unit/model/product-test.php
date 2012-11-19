<?php

require_once 'base-database-test.php';

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
     * Test Getting a ticket
     */
    public function testGet() {
        $this->product->get(36385);

        $this->assertEquals( $this->product->name, 'ZZZZZZZZZ Test' );
    }

    /**
     * Test Getting images
     *
     * @depends testGet
     */
    public function testGetImages() {
        // Declare variables
        $product_id = 4;

        // Load Product
        $this->product->get( $product_id );

        // Get images
        $images = $this->product->get_images();

        $this->assertEquals( count( $images ), 3 );
        $this->assertTrue( is_string( $images[0] ) );
    }

    /**
     * Test creating a company
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->product->website_id = 0;
        $this->product->user_id_created = 1;
        $this->product->create();

        $this->assertTrue( !is_null( $this->product->id ) );

        // Make sure it's in the database
        $this->product->get( $this->product->id );

        $this->assertEquals( 'deleted', $this->product->publish_visibility );

        // Delete the company
        $this->db->delete( 'products', array( 'product_id' => $this->product->id ), 'i' );
    }

    /**
     * Test adding a category
     *
     * This will be removed when the DB structure is changed
     *
     * @depends testGet
     */
    public function testAddCategory() {
        // Declare variables
        $product_id = 1;
        $category_id = '-5';

        // Delete any categories that may already exist
        $this->db->delete( 'product_categories', array( 'product_id' => $product_id ) , 'i' );

        // Get product
        $this->product->get( $product_id );

        // Add Category
        $this->product->add_category( $category_id );

        // See if it's there
        $fetched_category_id = $this->db->get_var( "SELECT `category_id` FROM `product_categories` WHERE `product_id` = $product_id" );

        $this->assertEquals( $category_id, $fetched_category_id );
    }

    /**
     * Test Adding Images
     *
     * @depends testGet
     */
    public function testAddImages() {
        // Declare variables
        $product_id = 1;
        $images = array( 'test.png', 'test1.gif' );

        // Delete any images from before hand
        $this->db->delete( 'product_images', array( 'product_id' => $product_id ) , 'i' );

        // Get product
        $this->product->get( $product_id );

        // Add images
        $this->product->add_images( $images );

        // See if they are there
        $fetched_images = $this->db->get_col( "SELECT `image` FROM `product_images` WHERE `product_id` = $product_id ORDER BY `image` ASC" );

        $this->assertEquals( $images, $fetched_images );
    }

    /**
     * Test updating a product
     *
     * @depends testGet
     */
    public function testUpdate() {
        // Declare variables
        $product_id = 36385;

        $this->db->update( 'products', array( 'publish_visibility' => 'deleted' ), array( 'product_id' => $product_id ), 's', 'i' );

        $this->product->get( $product_id );

        // Update test
        $this->product->publish_visibility = 'public';
        $this->product->save();

        $publish_visibility = $this->db->get_var( "SELECT `publish_visibility` FROM `products` WHERE `product_id` = $product_id" );

        $this->assertEquals( $publish_visibility, 'public' );
    }

    /**
     * Test Delete Categories
     *
     * @depends testGet
     * @depends testAddCategory
     */
    public function testDeleteCategories() {
        // Declare Variables
        $product_id = 1;
        $category_id = -6;

        // Get product
        $this->product->get( $product_id );

        // Add category
        $this->product->add_category( $category_id );

        // Delete categories
        $this->product->delete_categories();

        // Make sure there are no categories
        $category_ids = $this->db->get_col( "SELECT `category_id` FROM `product_categories` WHERE `product_id` = $product_id" );

        $this->assertTrue( 0 == count( $category_ids ) );
    }

    /**
     * Test Delete Images
     *
     * @depends testGet
     * @depends testAddImages
     */
    public function testDeleteImages() {
        // Declare Variables
        $product_id = 1;
        $images = array( 'no.jpg', 'yes.png' );

        // Get product
        $this->product->get( $product_id );

        // Add category
        $this->product->add_images( $images );

        // Delete images
        $this->product->delete_images();

        // Make sure there are no images
        $images = $this->db->get_col( "SELECT `image` FROM `product_images` WHERE `product_id` = $product_id" );

        $this->assertTrue( 0 == count( $images ) );
    }

    /**
     * Test Cloning a product
     */
    public function testCloneProduct() {
        $this->product->clone_product( 36385, 1 );

        $name = $this->db->get_var( 'SELECT `name` FROM `products` WHERE `product_id` = ' . (int) $this->product->id );

        $this->assertEquals( $name, 'ZZZZZZZZZ Test (Clone)' );

        $this->db->delete( 'products', array( 'product_id' => $this->product->id ), 'i' );
    }

    /**
     * Test listing all products
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
        $dt->order_by( 'p.`name`', 'u.`contact_name`', 'u2.`contact_name`', 'b.`name`', 'p.`sku`', 'c.`name`' );

        $products = $this->product->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( $products[0] instanceof Product );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Test counting the products
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
        $dt->order_by( 'a.`name`', 'e.`contact_name`', 'f.`contact_name`', 'd.`name`', 'a.`sku`', 'a.`status`' );

        $count = $this->product->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Test Autocomplete
     */
    public function testAutocomplete() {
        $products = $this->product->autocomplete( '1111', 'p.`sku`', 'sku', '' );

        $this->assertEquals( $products[0]['sku'], '11115' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->product = null;
    }
}
