<?php

require_once 'base-database-test.php';

class WebsiteCouponTest extends BaseDatabaseTest {
    /**
     * @var WebsiteCoupon
     */
    private $website_coupon;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_coupon = new WebsiteCoupon();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $website_id = -5;
        $name = 'Gumdrops';

        // Create
        $website_coupon_id = $this->db->insert( 'website_coupons', compact( 'website_id', 'name' ), 'is' );

        // Get
        $this->website_coupon->get( $website_coupon_id, $website_id );

        $this->assertEquals( $name, $this->website_coupon->name );

        // Delete
        $this->db->delete( 'website_coupons', compact( 'website_coupon_id' ), 'i' );
    }

    /**
     * Test Get By Product
     */
    public function testGetByProduct() {
        // Declare variables
        $website_id = -7;
        $name = 'Gumdrops';
        $product_id = -9;

        // Create
        $website_coupon_id = $this->db->insert( 'website_coupons', compact( 'website_id', 'name' ), 'is' );
        $this->db->insert( 'website_coupon_relations', compact( 'website_coupon_id', 'product_id' ), 'ii' );

        // Get by products
        $website_coupons = $this->website_coupon->get_by_product( $website_id, $product_id );
        $current_website_coupon = current( $website_coupons );

        $this->assertEquals( $name, $current_website_coupon );

        // Delete
        $this->db->delete( 'website_coupons', compact( 'website_id' ), 'i' );
        $this->db->delete( 'website_coupon_relations', compact( 'product_id' ), 'i' );
    }

    /**
     * Test Get By Account
     */
    public function testGetByAccount() {
        // Declare variables
        $website_id = -7;

        // Create
        $this->db->insert( 'website_coupons', compact( 'website_id' ), 'i' );

        // Get
        $website_coupons = $this->website_coupon->get_by_account( $website_id );

        $this->assertTrue( current( $website_coupons ) instanceof WebsiteCoupon );

        // Delete
        $this->db->delete( 'website_coupons', compact( 'website_id' ), 'i' );
    }

    /**
     * Getting Free Shipping Methods
     */
    public function testGetFreeShippingMethods() {
        // Declare variables
        $website_coupon_id = -7;

        // Setup id
        $this->website_coupon->id = $website_coupon_id;

        // Create
        $website_shipping_method_id = $this->db->insert( 'website_coupon_shipping_methods', compact( 'website_coupon_id' ), 'i' );

        // Get
        $website_shipping_method_ids = $this->website_coupon->get_free_shipping_methods( );

        $this->assertEquals( array( $website_shipping_method_id ), $website_shipping_method_ids );

        // Delete
        $this->db->delete( 'website_coupon_shipping_methods', compact( 'website_coupon_id' ), 'i' );
    }

    /**
     * Create
     */
    public function testCreate() {
        // Declare variables
        $original_name = 'Gumdrop';
        $website_id = -3;

        // Create test
        $this->website_coupon->website_id = $website_id;
        $this->website_coupon->name = $original_name;
        $this->website_coupon->create();

        $this->assertTrue( !is_null( $this->website_coupon->id ) );

        // Get the message
        $name = $this->db->get_var( 'SELECT `name` FROM `website_coupons` WHERE `website_coupon_id` = ' . (int) $this->website_coupon->id );

        $this->assertEquals( $original_name, $name );

        // Delete the note
        $this->db->delete( 'website_coupons', compact( 'website_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $website_id = -3;
        $original_name = 'Gumdrop';
        $new_name = 'pordmuG';

        // Create test
        $this->website_coupon->website_id = $website_id;
        $this->website_coupon->name = $original_name;
        $this->website_coupon->create();

        // Update test
        $this->website_coupon->name = $new_name;
        $this->website_coupon->save();

        // Now check it!
        $this->website_coupon->get( $this->website_coupon->id, $website_id );

        $this->assertEquals( $new_name, $this->website_coupon->name );

        // Delete the attribute item
        $this->db->delete( 'website_coupons', compact( 'website_id' ), 'i' );
    }

    /**
     * Add Relations
     */
    public function testAddRelations() {
        // Declare variables
        $product_id = -5;
        $website_coupon_ids = array( -2, -4, -6 );

        // Clear it just in case
        $this->db->delete( 'website_coupon_relations', compact( 'product_id' ), 'i' );

        // Add relations
        $this->website_coupon->add_relations( $product_id, $website_coupon_ids );

        // See if they are still there
        $retrieved_website_coupon_ids = $this->db->get_col( 'SELECT `website_coupon_id` FROM `website_coupon_relations` WHERE `product_id` = ' . (int) $product_id . ' ORDER BY `website_coupon_id` DESC' );

        // Make sure they are equal
        $this->assertEquals( $website_coupon_ids, $retrieved_website_coupon_ids );

        // Clean up
        $this->db->delete( 'website_coupon_relations', compact( 'product_id' ), 'i' );
    }

    /**
     * Add Free Shipping Methods
     */
    public function testAddFreeShippingMethods() {
        // Declare variables
        $website_coupon_id = -5;
        $website_shipping_method_ids = array( -12, -14, -16 );

        // Add relations
        $this->website_coupon->id = $website_coupon_id;
        $this->website_coupon->add_free_shipping_methods( $website_shipping_method_ids );

        // See if they are still there
        $retrieved_website_shipping_method_ids = $this->db->get_col( 'SELECT `website_shipping_method_id` FROM `website_coupon_shipping_methods` WHERE `website_coupon_id` = ' . (int) $website_coupon_id . ' ORDER BY `website_shipping_method_id` DESC' );

        // Make sure they are equal
        $this->assertEquals( $website_shipping_method_ids, $retrieved_website_shipping_method_ids );

        // Clean up
        $this->db->delete( 'website_coupon_shipping_methods', compact( 'website_coupon_id' ), 'i' );
    }

    /**
     * Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Declare variables
        $website_id = -3;

        // Create test
        $this->website_coupon->website_id = $website_id;
        $this->website_coupon->create();

        // Get variables
        $website_coupon_id = (int) $this->website_coupon->id;

        // Remove
        $this->website_coupon->remove();

        // Make sure it's not there
        $website_id = $this->db->get_var( "SELECT `website_id` FROM `website_coupons` WHERE `website_coupon_id` = $website_coupon_id" );

        $this->assertFalse( $website_id );
    }

    /**
     * Delete Relations by product
     *
     * @depends testCreate
     * @depends testAddRelations
     */
    public function testDeleteRelationsByProduct() {
        // Declare variables
        $website_id = -3;
        $product_id = -15;
        $website_coupon_ids = array();

        // Create test
        $this->website_coupon->website_id = $website_id;
        $this->website_coupon->create();

        $website_coupon_ids[] = $this->website_coupon->id;

        // Add relations
        $this->website_coupon->add_relations( $product_id, $website_coupon_ids );

        // Delete relations
        $this->website_coupon->delete_relations_by_product( $website_id, $product_id );

        $retrieved_website_coupon_ids = $this->db->get_col( 'SELECT `website_coupon_id` FROM `website_coupon_relations` WHERE `product_id` = ' . (int) $product_id );

        $this->assertTrue( empty( $retrieved_website_coupon_ids ) );

        $this->db->delete( 'website_coupons', array( 'website_coupon_id' => $this->website_coupon->id ), 'i' );
    }

    /**
     * Delete Free Shipping Methods
     *
     * @depends testAddFreeShippingMethods
     */
    public function testDeleteFreeShippingMethods() {
       // Declare variables
       $website_coupon_id = -15;
       $website_shipping_method_ids = array( -12, -14, -16 );

       // Add relations
       $this->website_coupon->id = $website_coupon_id;
       $this->website_coupon->add_free_shipping_methods( $website_shipping_method_ids );

        // Delete relations
        $this->website_coupon->delete_free_shipping_methods();

        $retrieved_website_shipping_methods = $this->db->get_col( 'SELECT `website_shipping_method_id` FROM `website_coupon_shipping_methods` WHERE `website_coupon_id` = ' . (int) $this->website_coupon->id );

        $this->assertTrue( empty( $retrieved_website_shipping_methods ) );
    }

    /**
     * List All
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
        $dt->order_by( '`name`', '`amount`', '`type`', '`item_limit`', '`date_created`' );

        $website_coupons = $this->website_coupon->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $website_coupons ) instanceof WebsiteCoupon );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
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
        $dt->order_by( '`name`', '`amount`', '`type`', '`item_limit`', '`date_created`' );

        $count = $this->website_coupon->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_coupon = null;
    }
}
