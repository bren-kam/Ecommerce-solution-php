<?php
require_once 'base-database-test.php';

class AttributeTest extends BaseDatabaseTest {
    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->attribute = new Attribute();
    }

    /**
     * Test Getting an attribute
     */
    public function testGet() {
        $this->attribute->get(137);

        $this->assertEquals( $this->attribute->title, 'Color - Leather' );
    }

    /**
     * Test Deleting an attribute
     *
     * @depends testGet
     */
    public function testDelete() {
        // Create attribute
        $this->db->insert( 'attributes', array( 'brand_id' => 0, 'title' => 'Temp Test', 'name' => 'Temp' ), 'iss' );

        $attribute_id = $this->db->get_insert_id();

        // Get it
        $this->attribute->get( $attribute_id );

        // Delete
        $this->attribute->delete();

        // Make sure it doesn't exist
        $title = $this->db->get_var( "SELECT `title` FROM `attributes` WHERE `attribute_id` = $attribute_id" );

        $this->assertFalse( $title );
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
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        $attributes = $this->attribute->list_all( $dt->get_variables() );

        // Make sure they exist
        $this->assertTrue( $attributes[0] instanceof Attribute );

        // Get rid of everything
        unset( $user, $_GET, $dt, $attributes );
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
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        $count = $this->attribute->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->attribute = null;
    }
}
