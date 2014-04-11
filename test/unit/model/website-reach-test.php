<?php

require_once 'test/base-database-test.php';

// Stub functions
if ( !function_exists( '_' ) ) {
    function _( $string ) {
        return $string;
    }
}

class WebsiteReachTest extends BaseDatabaseTest {
    const MESSAGE = 'What is the price on this one?';

    // Website Reach meta
    const WEBSITE_REACH_ID = 13;
    const KEY = 'type';
    const VALUE = 'quote';

    /**
     * @var WebsiteReach
     */
    private $website_reach;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->website_reach = new WebsiteReach();

        // Define
        $this->phactory->define( 'website_reaches', array( 'website_id' => self::WEBSITE_ID, 'message' => self::MESSAGE ) );
        $this->phactory->define( 'website_reach_meta', array( 'website_reach_id' => self::WEBSITE_REACH_ID, 'key' => self::KEY, 'value' => self::VALUE ) );
        $this->phactory->recall();
    }


    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_reach = $this->phactory->create('website_reaches');

        // Get
        $this->website_reach->get( $ph_website_reach->website_reach_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::MESSAGE, $this->website_reach->message );
    }

    /**
     * Get Meta
     */
    public function testGetMeta() {
        // Create
        $this->phactory->create('website_reach_meta');

        // Get
        $this->website_reach->id = self::WEBSITE_REACH_ID;
        $this->website_reach->get_meta();

        // Assert
        $this->assertEquals( self::VALUE, $this->website_reach->meta[self::KEY] );
    }

    /**
     * Get Info
     *
     * @depends testGetMeta
     */
    public function testGetInfo() {
        // Declare
        $brand = 'Astlow Signo';

        // Create
        $this->phactory->create( 'website_reach_meta' );
        $this->phactory->create( 'website_reach_meta', array( 'key' => 'product-link', 'value' => 'http://unlocked.blinkyblinky.me/product/asdasd' ) );
        $this->phactory->create( 'website_reach_meta', array( 'key' => 'product-name', 'value' => 'googoo' ) );
        $this->phactory->create( 'website_reach_meta', array( 'key' => 'product-sku', 'value' => 'Y123-B6' ) );
        $this->phactory->create( 'website_reach_meta', array( 'key' => 'product-brand', 'value' => $brand ) );
        $this->phactory->create( 'website_reach_meta', array( 'key' => 'location', 'value' => 'New York City' ) );

        // Get
        $this->website_reach->id = self::WEBSITE_REACH_ID;
        $this->website_reach->get_info();

        // Assert
        $this->assertEquals( $brand, $this->website_reach->info['Brand'] );
    }

    /**
     * Get Friendly Type
     */
    public function testGetFriendlyType() {
        // Get friendly type
        $type = $this->website_reach->get_friendly_type();
        $expected_type = 'Reach';

        // Assert
        $this->assertEquals( $expected_type, $type );

        // Get friendly type
        $this->website_reach->meta['type'] = self::VALUE;
        $type = $this->website_reach->get_friendly_type();
        $expected_type = 'Quote';

        // Assert
        $this->assertEquals( $expected_type, $type );
    }

    /**
     * Save
\     */
    public function testSave() {
        // Create
        $ph_website_reach = $this->phactory->create('website_reaches');

        // Save
        $this->website_reach->id = $ph_website_reach->website_reach_id;
        $this->website_reach->priority = WebsiteReach::PRIORITY_URGENT;
        $this->website_reach->save();

        // Get
        $ph_website_reach = $this->phactory->get( 'website_reaches', array( 'website_reach_id' => $ph_website_reach->website_reach_id ) );

        // Assert
        $this->assertEquals( $this->website_reach->priority, $ph_website_reach->priority );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Get User
        $stub_user = $this->getMock('User');

        // Create
        $ph_website_reach = $this->phactory->create('website_reaches');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'name', 'wu.`email`', 'wr.`assigned_to`', 'wr.`status`', 'wr.`priority`', 'wr.`date_created`' );

        // Website Reaches
        $website_reaches = $this->website_reach->list_all( $dt->get_variables() );
        $website_reach = current( $website_reaches );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteReach', $website_reaches );
        $this->assertEquals( $ph_website_reach->website_reach_id, $website_reach->website_reach_id );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_reaches');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'name', 'wu.`email`', 'wr.`assigned_to`', 'wr.`status`', 'wr.`priority`', 'wr.`date_created`' );

        // Get
        $count = $this->website_reach->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->website_reach = null;
    }
}
