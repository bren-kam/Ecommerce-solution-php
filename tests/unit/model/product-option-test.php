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
     * Test Deleting a Product Option
     *
     * @depends testGet
     */
    public function testDelete() {
        // Create product option
        $this->db->insert( 'product_options', array( 'type' => 'select', 'title' => 'Temp Color Test', 'name' => 'Color Test' ), 'sss' );

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

        // Make sure there are no list items
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
