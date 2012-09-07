<?php
require_once 'base-database-test.php';

class BrandTest extends BaseDatabaseTest {
    /**
     * @var Brand
     */
    private $brand;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->brand = new Brand();
    }

    /**
     * Test Getting a Brand
     */
    public function testGet() {
        $brand_id = 8;

        $this->brand->get( $brand_id );

        $this->assertEquals( $this->brand->name, 'Ashley Furniture' );
    }

    /**
     * Test Getting all of some brands
     */
    public function testGetAll() {
        $brands = $this->brand->get_all();

        $this->assertTrue( $brands[0] instanceof Brand );
    }

    /**
     * Test Getting relations (to product options)
     *
     * @depends testGet
     */
    public function testGetProductOptionRelations() {
        // Get a brand
        $brand_id = 8;
        $this->brand->get( $brand_id );

        $product_option_ids = $this->brand->get_product_option_relations();

        $this->assertTrue( is_array( $product_option_ids ) );
        $this->assertEquals( count( $product_option_ids ), 7 );
    }

    /**
     * Test creating an attribute
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->brand->name = 'Test Brand';
        $this->brand->slug = 'test-brand';
        $this->brand->link = 'www.testbrand.com';
        $this->brand->image = '';
        $this->brand->create();

        $this->assertTrue( !is_null( $this->brand->id ) );

        // Make sure it's in the database
        $this->brand->get( $this->brand->id );

        $this->assertEquals( 'www.testbrand.com', $this->brand->link );

        // Delete the brand
        $this->db->delete( 'brands', array( 'brand_id' => $this->brand->id ), 'i' );
    }

    /**
     * Test Adding Product Option Relations
     *
     * @depends testGetProductOptionRelations
     */
    public function testAddProductOptionRelations() {
        // Declare variables
        $brand_id = 612;
        $product_option_ids = array( '-2', '-1' );

        // Delete any previous relations
        $this->db->delete( 'product_option_relations', array( 'brand_id' => 612 ), 'i' );

        // Get brand
        $this->brand->get( $brand_id );

        // Add them
        $this->brand->add_product_option_relations( $product_option_ids );

        // Now check it
        $fetched_product_option_ids = $this->brand->get_product_option_relations( $brand_id );

        $this->assertEquals( $product_option_ids, $fetched_product_option_ids );
    }

    /**
     * Test updating an attribute
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $this->brand->name = 'Test Brand';
        $this->brand->slug = 'test-brand';
        $this->brand->link = 'www.testbrand.com';
        $this->brand->image = '';
        $this->brand->create();

        // Update test
        $this->brand->slug = 'dnarb-tset';
        $this->brand->update();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->brand->id ) );

        // Now check it!
        $this->brand->get( $this->brand->id );

        $this->assertEquals( 'dnarb-tset', $this->brand->slug );

        // Delete the brand
        $this->db->delete( 'brands', array( 'brand_id' => $this->brand->id ), 'i' );
    }

    /**
     * Test Deleting an attribute
     *
     * @depends testGet
     */
    public function testDelete() {
        // Create Brand
        $this->db->insert( 'brands', array( 'name' => 'Test Brand', 'slug' => 'test-brand', 'link' => '', 'image' => '' ), 'ssss' );

        $brand_id = $this->db->get_insert_id();

        // Get it
        $this->brand->get( $brand_id );

        // Delete
        $this->brand->delete();

        // Make sure it doesn't exist
        $name = $this->db->get_var( "SELECT `name` FROM `brands` WHERE `brand_id` = $brand_id" );

        $this->assertFalse( $name );
    }

    /**
     * Test Delete relations
     *
     * @depends testAddProductOptionRelations
     */
    public function testDeleteProductOptionRelations() {
        // Declare variables
        $brand_id = 612;
        $product_option_ids = array( '-1', '-2' );

        // Get the brand
        $this->brand->get( $brand_id );

        // Add relations
        $this->brand->add_product_option_relations( $product_option_ids );

        // Delete relations
        $this->brand->delete_product_option_relations();

        // Get relations
        $fetched_product_option_ids = $this->brand->get_product_option_relations();

        $this->assertTrue( is_array( $fetched_product_option_ids ) );
        $this->assertEquals( count( $fetched_product_option_ids ), 0 );
    }

    /**
     * Test Listing all the attributes
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`name`', '`link`' );
        $dt->search( array( '`name`' => true, '`link`' => true ) );

        $brands = $this->brand->list_all( $dt->get_variables() );

        // Make sure they exist
        $this->assertTrue( $brands[0] instanceof Brand );

        // Get rid of everything
        unset( $user, $_GET, $dt, $brands );
    }

    /**
     * Test counting all the attributes
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`name`', '`link`' );
        $dt->search( array( '`name`' => true, '`link`' => true ) );

        $count = $this->brand->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->brand = null;
    }
}
