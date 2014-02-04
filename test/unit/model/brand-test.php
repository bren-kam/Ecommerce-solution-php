<?php
require_once 'test/base-database-test.php';

class BrandTest extends BaseDatabaseTest {
    const NAME = 'Smurfy Tools';

    // Product option relations
    const BRAND_ID = 13;
    const PRODUCT_OPTION_ID = 15;

    /**
     * @var Brand
     */
    private $brand;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->brand = new Brand();

        // Define
        $this->phactory->define( 'brands', array( 'name' => self::NAME ) );
        $this->phactory->define( 'product_option_relations', array( 'brand_id' => self::BRAND_ID, 'product_option_id' => self::PRODUCT_OPTION_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting a Brand
     */
    public function testGet() {
        // Create
        $ph_brand = $this->phactory->create('brands');

        // Get
        $this->brand->get( $ph_brand->brand_id );

        // Assert
        $this->assertEquals( self::NAME, $this->brand->name );
    }

    /**
     * Test Getting all of some brands
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('brands');

        // Get
        $brands = $this->brand->get_all();
        $brand = current( $brands );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Brand', $brands );
        $this->assertEquals( self::NAME, $brand->name );
    }

    /**
     * Test Getting relations (to product options)
     *
     * @depends testGet
     */
    public function testGetProductOptionRelations() {
        // Create
        $this->phactory->create('product_option_relations');

        // Get
        $this->brand->id = self::BRAND_ID;
        $product_option_ids = $this->brand->get_product_option_relations();
        $expected_product_option_ids = array( self::PRODUCT_OPTION_ID );

        $this->assertEquals( $expected_product_option_ids, $product_option_ids );
    }

    /**
     * Test creating
     */
    public function testCreate() {
        // Create
        $this->brand->name = self::NAME;
        $this->brand->create();

        $this->assertNotNull( $this->brand->id );

        // Make sure it's in the database
        $ph_brand = $this->phactory->get( 'brands', array( 'brand_id' => $this->brand->id ) );

        $this->assertEquals( self::NAME, $ph_brand->name );
    }

    /**
     * Test Adding Product Option Relations
     */
    public function testAddProductOptionRelations() {
        // Add them
        $this->brand->id = self::BRAND_ID;
        $this->brand->add_product_option_relations( array( self::PRODUCT_OPTION_ID ) );

        // Now check it
        $ph_product_option_relation = $this->phactory->get( 'product_option_relations', array( 'brand_id' => self::BRAND_ID ) );

        $this->assertEquals( self::PRODUCT_OPTION_ID, $ph_product_option_relation->product_option_id );
    }

    /**
     * Test updating an attribute
     */
    public function testUpdate() {
        // Create
        $ph_brand = $this->phactory->create('brands');

        // Update test
        $this->brand->id = $ph_brand->brand_id;
        $this->brand->slug = 'dnarb-tset';
        $this->brand->save();

        // Now check it!
        $ph_brand = $this->phactory->get( 'brands', array( 'brand_id' => $ph_brand->brand_id ) );

        // Assert
        $this->assertEquals( $this->brand->slug, $ph_brand->slug );
    }

    /**
     * Test Deleting
     *
     * @depends testGet
     */
    public function testDelete() {
        // Create
        $ph_brand = $this->phactory->create('brands');

        // Delete
        $this->brand->id = $ph_brand->brand_id;
        $this->brand->remove();

        // Now check it!
        $ph_brand = $this->phactory->get( 'brands', array( 'brand_id' => $ph_brand->brand_id ) );

        // Assert
        $this->assertNull( $ph_brand );
    }

    /**
     * Test Delete relations
     *
     * @depends testAddProductOptionRelations
     */
    public function testDeleteProductOptionRelations() {
        // Create
        $this->phactory->create('product_option_relations');

        // Delete relations
        $this->brand->id = self::BRAND_ID;
        $this->brand->delete_product_option_relations();

        // Now check it!
        $ph_product_option_relation = $this->phactory->get( 'product_option_relations', array( 'brand_id' => self::BRAND_ID ) );

        // Assert
        $this->assertNull( $ph_product_option_relation );
    }

    /**
     * Test Listing all the attributes
     */
    public function testListAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('brands');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`link`' );
        $dt->search( array( '`name`' => true, '`link`' => true ) );

        $brands = $this->brand->list_all( $dt->get_variables() );
        $brand = current( $brands );

        // Make sure they exist
        $this->assertContainsOnlyInstancesOf( 'Brand', $brands );
        $this->assertEquals( self::NAME, $brand->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $brands );
    }

    /**
     * Test counting all the attributes
     */
    public function testCountAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('brands');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`link`' );
        $dt->search( array( '`name`' => true, '`link`' => true ) );

        $count = $this->brand->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

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
