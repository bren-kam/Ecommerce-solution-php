<?php
require_once 'test/base-database-test.php';

class ProductOptionTest extends BaseDatabaseTest {
    const TITLE = 'Sofa Colors';
    const NAME = 'Colors';

    // Product Option Relations
    const BRAND_ID = 13;

    /**
     * @var ProductOption
     */
    private $product_option;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->product_option = new ProductOption();

        // Define
        $this->phactory->define( 'product_options', array( 'option_title' => self::TITLE, 'option_name' => self::NAME ) );
        $this->phactory->define( 'product_option_relations', array( 'brand_id' => self::BRAND_ID ) );
        $this->phactory->define( 'product_option_list_items' );
        $this->phactory->define( 'products', array( 'brand_id' => self::BRAND_ID ) );
        $this->phactory->recall();
    }


    /**
     * Test Getting an attribute
     */
    public function testGet() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');

        // Get
        $this->product_option->get( $ph_product_option->product_option_id );

        // Assert
        $this->assertEquals( self::TITLE, $this->product_option->title );
    }

    /**
     * Test Getting all product options
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('product_options');

        // Get
        $product_options = $this->product_option->get_all();
        $product_option = current( $product_options );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ProductOption', $product_options );
        $this->assertEquals( self::TITLE, $product_option->title );
    }

    /**
     * Get By Product
     */
    public function testGetByProduct() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');
        $this->phactory->create( 'product_option_relations', array( 'product_option_id' => $ph_product_option->product_option_id ) );
        $ph_product = $this->phactory->create( 'products', compact( 'brand_id' ), 'i' );

        // Get product Options
        $product_options = $this->product_option->get_by_product( $ph_product->product_id );
        $product_option = current( $product_options );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ProductOption', $product_options );
        $this->assertEquals( self::NAME, $product_option->name );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->product_option->name = self::NAME;
        $this->product_option->create();

        // Assert
        $this->assertNotNull( $this->product_option->id );

        // Get
        $ph_product_option = $this->phactory->get( 'product_options', array( 'product_option_id' => $this->product_option->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_product_option->option_name );
    }

    /**
     * Test updating the product option
\     */
    public function testUpdate() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');

        // Update
        $this->product_option->id = $ph_product_option->product_option_id;
        $this->product_option->name = 'Shades';
        $this->product_option->save();

        // Get
        $ph_product_option = $this->phactory->get( 'product_options', array( 'product_option_id' => $this->product_option->id ) );

        // Assert
        $this->assertEquals( $this->product_option->name, $ph_product_option->option_name );
    }

    /**
     * Test Deleting a Product Option
     */
    public function testDelete() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');

        // Delete
        $this->product_option->id = $ph_product_option->product_option_id;
        $this->product_option->remove();

        // Get
        $ph_product_option = $this->phactory->get( 'product_options', array( 'product_option_id' => $this->product_option->id ) );

        // Assert
        $this->assertNull( $ph_product_option );
    }

    /**
     * Test Deleting product option relations
     */
    public function testDeleteRelationsByBrand() {
        // Create
        $this->phactory->create('product_option_relations');

        // Delete relations
        $this->product_option->delete_relations_by_brand( self::BRAND_ID );

        // Get
        $ph_product_option_relation = $this->phactory->get( 'product_option_relations', array( 'brand_id' => self::BRAND_ID ) );

        // Assert
        $this->assertNull( $ph_product_option_relation );
    }

    /**
     * Test Listing all the attributes
     */
    public function testListAll() {
        // Get Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('product_options');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`title`', '`name`', '`type`' );
        $dt->search( array( '`title`' => true, '`name`' => true, '`type`' => true ) );

        // Get
        $product_options = $this->product_option->list_all( $dt->get_variables() );
        $product_option = current( $product_options );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ProductOption', $product_options );
        $this->assertEquals( self::TITLE, $product_option->title );

        // Get rid of everything
        unset( $user, $_GET, $dt, $product_options );
    }

    /**
     * Test counting all the attributes
     */
    public function testCountAll() {
        // Get Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('product_options');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`title`', '`name`', '`type`' );
        $dt->search( array( '`title`' => true, '`name`' => true, '`type`' => true ) );

        // Get
        $count = $this->product_option->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->product_option = null;
    }
}
