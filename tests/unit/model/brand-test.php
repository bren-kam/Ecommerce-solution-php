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
