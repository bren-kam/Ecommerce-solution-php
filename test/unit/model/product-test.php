<?php

require_once 'test/base-database-test.php';

class ProductTest extends BaseDatabaseTest {
    const CATEGORY_ID = 3;
    const NAME = 'Owlean Reclining Sofa';
    const SKU = 'RJ123-SOFA';

    // Product Images
    const PRODUCT_ID = 17;
    const IMAGE = 'reclining-large.png';
    const SEQUENCE = 0;

    /**
     * @var Product
     */
    private $product;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->product = new Product();

        // Define
        $this->phactory->define( 'products', array( 'category_id' => self::CATEGORY_ID, 'name' => self::NAME, 'sku' => self::SKU, 'publish_visibility' => Product::PUBLISH_VISIBILITY_DELETED ) );
        $this->phactory->define( 'product_images', array( 'product_id' => self::PRODUCT_ID, 'image' => self::IMAGE, 'sequence' => self::SEQUENCE ) );
        $this->phactory->recall();
    }


    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_product = $this->phactory->create('products');

        // Get
        $this->product->get( $ph_product->product_id );

        // Assert
        $this->assertEquals( self::NAME, $this->product->name );
    }

    /**
     * Get By Sku
     */
    public function testGetBySku() {
        // Create
        $this->phactory->create('products');

        // Get
        $this->product->get_by_sku( self::SKU );

        // Assert
        $this->assertEquals( self::NAME, $this->product->name );
    }

    /**
     * Get By Ids
     */
    public function testGetByIds() {
        // Create
        $ph_product = $this->phactory->create('products');

        // Get
        $products = $this->product->get_by_ids( array( $ph_product->product_id ) );
        $product = current( $products );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Product', $products );
        $this->assertEquals( self::NAME, $product->name );
    }

    /**
     * Test Getting images
     *
     * @depends testGet
     */
    public function testGetImages() {
        // Create
        $this->phactory->create('product_images');

        // Get images
        $this->product->id = self::PRODUCT_ID;
        $images = $this->product->get_images();
        $expected_images = array( self::IMAGE );

        // Assert
        $this->assertEquals( $expected_images, $images );
    }

    /**
     * Create
     */
    public function testCreate() {
        // Create
        $this->product->category_id = self::CATEGORY_ID;
        $this->product->create();

        // Assert
        $this->assertNotNull( $this->product->id );

        // Get
        $ph_product = $this->phactory->get( 'products', array( 'product_id' => $this->product->id ) );

        // Assert
        $this->assertEquals( self::CATEGORY_ID, $ph_product->category_id );
    }

    /**
     * Test Adding Images
     *
     * @depends testGet
     */
    public function testAddImages() {
        // Reset
        $this->phactory->recall();

        // Add images
        $this->product->id = self::PRODUCT_ID;
        $this->product->add_images( array( self::IMAGE ) );

        // Get
        $ph_product_image = $this->phactory->get( 'product_images', array( 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertEquals( self::IMAGE, $ph_product_image->image );
    }

    /**
     * Test updating a product
     */
    public function testSave() {
        // Create
        $ph_product = $this->phactory->create('products');

        // Save
        $this->product->id = $ph_product->product_id;
        $this->product->publish_visibility = 'public';
        $this->product->save();

        // Get
        $ph_product = $this->phactory->get( 'products', array( 'product_id' => $ph_product->product_id ) );

        // Assert
        $this->assertEquals( $this->product->publish_visibility, $ph_product->publish_visibility );
    }

    /**
     * Test Delete Images
     */
    public function testDeleteImages() {
        // Create
        $this->phactory->create('product_images');

        // Delete
        $this->product->id = self::PRODUCT_ID;
        $this->product->delete_images();

        // Get
        $ph_product_image = $this->phactory->get( 'product_images', array( 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertNull( $ph_product_image );
    }

    /**
     * Test Cloning a product
     */
    public function testCloneProduct() {
        // Declare
        $user_id = 6;

        // Create
        $ph_product = $this->phactory->create('products');

        // Clone
        $this->product->clone_product( $ph_product->product_id, $user_id );

        // Get
        $ph_product = $this->phactory->get( 'products', array( 'product_id' => $this->product->id ) );
        $expected_name = self::NAME . ' (Clone)';

        // Assert
        $this->assertEquals( $user_id, $ph_product->user_id_created );
        $this->assertEquals( $expected_name, $ph_product->name );
    }

    /**
     * Test listing all products
     */
    public function testListAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('products');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'p.`name`', 'u.`contact_name`', 'u2.`contact_name`', 'b.`name`', 'p.`sku`', 'c.`name`' );

        // Get
        $products = $this->product->list_all( $dt->get_variables() );
        $product = current( $products );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Product', $products );
        $this->assertEquals( self::NAME, $product->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Test counting the products
     */
    public function testCountAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('products');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'a.`name`', 'e.`contact_name`', 'f.`contact_name`', 'd.`name`', 'a.`sku`', 'a.`status`' );

        // Get
        $count = $this->product->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * List Custom Products
     */
    public function testListCustomProducts() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create( 'products', array( 'website_id' => self::WEBSITE_ID, 'publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->add_where( " AND p.`website_id` = " . (int) self::WEBSITE_ID );
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'c.`name`', 'p.`status`', 'p.`publish_date`' );

        // Get
        $products = $this->product->list_custom_products( $dt->get_variables() );
        $product = current( $products );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Product', $products );
        $this->assertEquals( self::NAME, $product->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $products );
    }

    /**
     * Count Custom Products
     */
    public function testCountCustomProducts() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create( 'products', array( 'website_id' => self::WEBSITE_ID, 'publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'c.`name`', 'p.`status`', 'p.`publish_date`' );

        // Get
        $count = $this->product->count_custom_products( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Test Autocomplete
     */
    public function testAutocomplete() {
        // Create
        $ph_product = $this->phactory->create('products');
        $this->phactory->create( 'product_images', array( 'product_id' => $ph_product->product_id ) );

        // Get
        $results = $this->product->autocomplete( substr( self::SKU, 0, 3 ), 'p.`sku`', 'sku', '' );
        $expected_results = array( array( 'sku' => self::SKU ) );

        // Assert
        $this->assertEquals( $expected_results, $results );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->product = null;
    }
}
