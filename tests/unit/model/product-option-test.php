<?php
require_once 'base-database-test.php';

class ProductOptionTest extends BaseDatabaseTest {
    /**
     * @var ProductOption
     */
    private $product_option;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->product_option = new ProductOption();
    }

    /**
     * Test Getting an attribute
     */
    public function testGet() {
        $this->product_option->get(66);

        $this->assertEquals( $this->product_option->title, 'Annual Sales' );
    }
    
    /**
     * Test Getting all product options
     */
    public function testGetAll() {
        $product_options = $this->product_option->get_all();

        $this->assertTrue( current( $product_options ) instanceof ProductOption );
    }

    /**
     * Test create
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->product_option->type = 'text';
        $this->product_option->title = 'Extra Information';
        $this->product_option->name = 'Extra Info';
        $this->product_option->create();

        $this->assertTrue( !is_null( $this->product_option->id ) );

        // Make sure it's in the database
        $this->product_option->get( $this->product_option->id );

        $this->assertEquals( 'Extra Info', $this->product_option->name );

        // Delete the product option
        $this->db->delete( 'product_options', array( 'product_option_id' => $this->product_option->id ), 'i' );
    }

    /**
     * Test updating the product option
     *
     * @depends testCreate
     */
    public function testUpdate() {
        $this->product_option->type = 'text';
        $this->product_option->title = 'Extra Information';
        $this->product_option->name = 'Extra Info';
        $this->product_option->create();

        // Update test
        $this->product_option->name = 'ofnI artxE';
        $this->product_option->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->product_option->id ) );

        // Now check it!
        $this->product_option->get( $this->product_option->id );

        $this->assertEquals( 'ofnI artxE', $this->product_option->name );

        // Delete the product option list item
        $this->db->delete( 'product_options', array( 'product_option_id' => $this->product_option->id ), 'i' );
    }

    /**
     * Test Deleting a Product Option
     *
     * @depends testGet
     */
    public function testDelete() {
        // Create product option
        $this->db->insert( 'product_options', array( 'option_type' => 'select', 'option_title' => 'Temp Color Test', 'option_name' => 'Color Test' ), 'sss' );

        $product_option_id = $this->db->get_insert_id();

        // Create other relations
        $this->db->insert( 'product_options_relations', array( 'product_option_id' => $product_option_id, 'brand_id' => -1 ), 'ii' );
        $this->db->insert( 'product_option_list_items', array( 'product_option_id' => $product_option_id, 'value' => 'Test Value', 'sequence' => 0 ), 'isi' );


        // Get it
        $this->product_option->get( $product_option_id );

        // Delete everything
        $this->product_option->delete();

        // Make sure the product option it doesn't exist
        $title = $this->db->get_var( "SELECT `title` FROM `product_options` WHERE `product_option_id` = $product_option_id" );

        $this->assertFalse( $title );

        // Make sure there are no relations
        $brand_id = $this->db->get_var( "SELECT `brand_id` FROM `product_option_relations` WHERE `product_option_id` = $product_option_id" );

        $this->assertFalse( $brand_id );

        // Make sure there are nos
        $value = $this->db->get_var( "SELECT `value` FROM `product_option_list_items` WHERE `product_option_id` = $product_option_id" );

        $this->assertFalse( $value );
    }

    /**
     * Test Deleting product option relations
     */
    public function testDeleteRelationsByBrand() {
        // Fake Brand ID
        $brand_id = -5;

        // Create relations
        $this->db->insert( 'product_options_relations', array( 'product_option_id' => 1, 'brand_id' => $brand_id ), 'ii' );

        // Delete relations
        $this->product_option->delete_relations_by_brand( $brand_id );

        // Make sure there are no relations
        $product_option_id = $this->db->get_var( "SELECT `product_option_id` FROM `product_option_relations` WHERE `brand_id` = $brand_id" );

        $this->assertFalse( $product_option_id );
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
        $dt->order_by( '`title`', '`name`', '`type`' );
        $dt->search( array( '`title`' => true, '`name`' => true, '`type`' => true ) );

        $product_options = $this->product_option->list_all( $dt->get_variables() );

        // Make sure they exist
        $this->assertTrue( $product_options[0] instanceof ProductOption );

        // Get rid of everything
        unset( $user, $_GET, $dt, $product_options );
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
        $dt->order_by( '`title`', '`name`', '`type`' );
        $dt->search( array( '`title`' => true, '`name`' => true, '`type`' => true ) );

        $count = $this->product_option->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

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
