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
     * Test updating a product
     *
     * @depends testGet
     */
    public function testUpdate() {
        $this->db->update( 'products', array( 'publish_visibility' => 'deleted' ), array( 'product_id' => 36385 ), 's', 'i' );

        $this->product->get(36385);

        // Update test
        $this->product->publish_visibility = 'public';
        $this->product->update();

        $publish_visibility = $this->db->get_var( 'SELECT `publish_visibility` FROM `products` WHERE `product_id` = 36385' );

        $this->assertEquals( $publish_visibility, 'public' );
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
        $dt->order_by( 'a.`name`', 'e.`contact_name`', 'f.`contact_name`', 'd.`name`', 'a.`sku`', 'a.`status`' );

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
