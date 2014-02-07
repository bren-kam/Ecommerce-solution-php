<?php

require_once 'test/base-database-test.php';

class WebsiteCouponTest extends BaseDatabaseTest {
    const NAME = 'Gumdrops';

    // Website Coupon Relations
    const WEBSITE_COUPON_ID = 13;
    const PRODUCT_ID = 15;

    // Website Coupon Shipping Methods
    const WEBSITE_SHIPPING_METHOD_ID = 27;

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

        // Define
        $this->phactory->define( 'website_coupons', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME ) );
        $this->phactory->define( 'website_coupon_relations', array( 'website_coupon_id' => self::WEBSITE_COUPON_ID, 'product_id' => self::PRODUCT_ID ) );
        $this->phactory->define( 'website_coupon_shipping_methods', array( 'website_coupon_id' => self::WEBSITE_COUPON_ID, 'website_shipping_method_id' => self::WEBSITE_SHIPPING_METHOD_ID ) );
        $this->phactory->recall();
    }


    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_website_coupon = $this->phactory->create('website_coupons');

        // Get
        $this->website_coupon->get( $ph_website_coupon->website_coupon_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->website_coupon->name );
    }

    /**
     * Test Get By Product
     */
    public function testGetByProduct() {
        // Create
        $ph_website_coupon = $this->phactory->create('website_coupons');
        $this->phactory->create( 'website_coupon_relations', array( 'website_coupon_id' => $ph_website_coupon->website_coupon_id ) );

        // Get
        $website_coupons = $this->website_coupon->get_by_product( self::WEBSITE_ID, self::PRODUCT_ID );
        $expected_coupons = array( $ph_website_coupon->website_coupon_id => self::NAME );

        // Assert
        $this->assertEquals( $expected_coupons, $website_coupons );
    }

    /**
     * Test Get By Account
     */
    public function testGetByAccount() {
        // Create
        $this->phactory->create('website_coupons');

        // Get
        $website_coupons = $this->website_coupon->get_by_account( self::WEBSITE_ID );
        $website_coupon = current( $website_coupons );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteCoupon', $website_coupons );
        $this->assertEquals( self::NAME, $website_coupon->name );
    }

    /**
     * Getting Free Shipping Methods
     */
    public function testGetFreeShippingMethods() {
        // Create
        $this->phactory->create('website_coupon_shipping_methods');

        // Get
        $this->website_coupon->id = self::WEBSITE_COUPON_ID;
        $website_shipping_method_ids = $this->website_coupon->get_free_shipping_methods();
        $expected_shipping_method_ids = array( self::WEBSITE_SHIPPING_METHOD_ID );

        // Assert
        $this->assertEquals( $expected_shipping_method_ids, $website_shipping_method_ids );
    }

    /**
     * Create
     */
    public function testCreate() {
        // Create
        $this->website_coupon->name = self::NAME;
        $this->website_coupon->create();

        // Assert
        $this->assertNotNull( $this->website_coupon->id );

        // Get
        $ph_website_coupon = $this->phactory->get( 'website_coupons', array( 'website_coupon_id' => $this->website_coupon->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_website_coupon->name );
    }

    /**
     * Save
     */
    public function testSave() {
        // Assert
        $ph_website_coupon = $this->phactory->create('website_coupons');

        // Save
        $this->website_coupon->id = $ph_website_coupon->website_coupon_id;
        $this->website_coupon->name = 'Savings Howl';
        $this->website_coupon->save();

        // Get
        $ph_website_coupon = $this->phactory->get( 'website_coupons', array( 'website_coupon_id' => $this->website_coupon->id ) );

        // Assert
        $this->assertEquals( $this->website_coupon->name, $ph_website_coupon->name );
    }

    /**
     * Add Relations
     */
    public function testAddRelations() {
        // Add relations
        $this->website_coupon->add_relations( self::PRODUCT_ID, array( self::WEBSITE_COUPON_ID ) );

        // Get
        $ph_website_coupon_relation = $this->phactory->get( 'website_coupon_relations', array( 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertEquals( self::WEBSITE_COUPON_ID, $ph_website_coupon_relation->website_coupon_id );
    }

    /**
     * Add Free Shipping Methods
     */
    public function testAddFreeShippingMethods() {
        // Add relations
        $this->website_coupon->id = self::WEBSITE_COUPON_ID;
        $this->website_coupon->add_free_shipping_methods( array( self::WEBSITE_SHIPPING_METHOD_ID ) );

        // Get
        $ph_website_coupon_shipping_method = $this->phactory->get( 'website_coupon_shipping_methods', array( 'website_coupon_id' => self::WEBSITE_COUPON_ID ) );

        // Assert
        $this->assertEquals( self::WEBSITE_SHIPPING_METHOD_ID, $ph_website_coupon_shipping_method->website_shipping_method_id );
    }

    /**
     * Delete Relations by product
     */
    public function testDeleteRelationsByProduct() {
        // Create
        $ph_website_coupon = $this->phactory->create('website_coupons');
        $this->phactory->create( 'website_coupon_relations', array( 'website_coupon_id' => $ph_website_coupon->website_coupon_id ) );

        // Delete relations
        $this->website_coupon->delete_relations_by_product( self::WEBSITE_ID, self::PRODUCT_ID );

        // Get
        $ph_website_coupon_relation = $this->phactory->get( 'website_coupon_relations', array( 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertNull( $ph_website_coupon_relation );
    }

    /**
     * Delete Free Shipping Methods
     */
    public function testDeleteFreeShippingMethods() {
        // Create
        $this->phactory->create('website_coupon_shipping_methods');

        // Delete relations
        $this->website_coupon->id = self::WEBSITE_COUPON_ID;
        $this->website_coupon->delete_free_shipping_methods();

        // Get
        $ph_website_coupon_relation = $this->phactory->get( 'website_coupon_shipping_methods', array( 'website_coupon_id' => self::WEBSITE_COUPON_ID ) );

        // Assert
        $this->assertNull( $ph_website_coupon_relation );
    }


    /**
     * List All
     */
    public function testListAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_coupons');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`amount`', '`type`', '`item_limit`', '`date_created`' );

        // Get
        $website_coupons = $this->website_coupon->list_all( $dt->get_variables() );
        $website_coupon = current( $website_coupons );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteCoupon', $website_coupons );
        $this->assertEquals( self::NAME, $website_coupon->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_coupons');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`amount`', '`type`', '`item_limit`', '`date_created`' );

        // Get
        $count = $this->website_coupon->count_all( $dt->get_count_variables() );

        // Assert
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
