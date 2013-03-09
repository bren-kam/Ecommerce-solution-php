<?php

require_once 'base-database-test.php';

// Stub functions
function _( $string ) {
    return $string;
}

class WebsiteReachTest extends BaseDatabaseTest {
    /**
     * @var WebsiteReach
     */
    private $website_reach;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_reach = new WebsiteReach();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $message = 'Grandma Shipping';

        // Create
        $website_reach_id = $this->db->insert( 'website_reaches', compact( 'website_id', 'message' ), 'is' );

        // Get
        $this->website_reach->get( $website_reach_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $message, $this->website_reach->message );

        // Clean up
        $this->db->delete( 'website_reaches', compact( 'website_id' ), 'i' );
    }

    /**
     * Get Meta
     */
    public function testGetMeta() {
        // Set variables
        $website_reach_id = -7;
        $key = 'test';
        $value = 'value';

        // Set ID
        $this->website_reach->id = $website_reach_id;

        // Create
        $this->db->insert( 'website_reach_meta', compact( 'website_reach_id', 'key', 'value' ), 'iss' );

        // Get
        $this->website_reach->get_meta();

        // Make sure we grabbed the right one
        $this->assertEquals( $value, $this->website_reach->meta[$key] );

        // Clean up
        $this->db->delete( 'website_reach_meta', compact( 'website_reach_id' ), 'i' );
    }

    /**
     * Get Info
     *
     * @depends testGetMeta
     */
    public function testGetInfo() {
        // Set variables
        $website_reach_id = -7;
        $product_sku = 'ga ga';

        // Set ID
        $this->website_reach->id = $website_reach_id;

        // Create the meta necessary
        $this->db->query( "INSERT INTO `website_reach_meta` ( `website_reach_id`, `key`, `value` ) VALUES ( $website_reach_id, 'type', 'quote' ), ( $website_reach_id, 'product-link', 'http://test.com/' ), ( $website_reach_id, 'product-name', 'goo goo' ), ( $website_reach_id, 'product-sku', '$product_sku' )" );

        // Get
        $this->website_reach->get_info();

        // Make sure we grabbed the right one
        $this->assertTrue( (bool) stristr( $this->website_reach->info['SKU'], $product_sku ) );

        // Clean up
        $this->db->delete( 'website_reach_meta', compact( 'website_reach_id' ), 'i' );
    }

    /**
     * Get Friendly Type
     *
     * @depends testGetMeta
     */
    public function testGetFriendlyType() {
        // Set variables
        $website_reach_id = -9;
        $friendly_type = 'Quote';

        // Set ID
        $this->website_reach->id = $website_reach_id;

        // Create the meta necessary
        $this->db->query( "INSERT INTO `website_reach_meta` ( `website_reach_id`, `key`, `value` ) VALUES ( $website_reach_id, 'type', 'quote' ) " );

        // Get meta
        $this->website_reach->get_meta();

        // Get friendly type
        $type = $this->website_reach->get_friendly_type();

        $this->assertEquals( $friendly_type, $type );

        // Clean up
        $this->db->delete( 'website_reach_meta', compact( 'website_reach_id' ), 'i' );
    }
    
    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Declare variables
        $website_id = -5;
        $priority = 5;

        // Create
        $website_reach_id = $this->db->insert( 'website_reaches', compact( 'website_id' ), 'i' );

        // Get
        $this->website_reach->get( $website_reach_id, $website_id );

        // Save
        $this->website_reach->priority = $priority;
        $this->website_reach->save();

        // Make sure it's in the database
        $retrieved_priority = $this->db->get_var( "SELECT `priority` FROM `website_reaches` WHERE `website_id` = $website_id" );

        $this->assertEquals( $priority, $retrieved_priority );

        // Clean up
        $this->db->delete( 'website_reaches', compact( 'website_id' ), 'i' );
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
        $dt->order_by( 'name', 'wu.`email`', 'wr.`assigned_to`', 'wr.`status`', 'wr.`priority`', 'wr.`date_created`' );

        $website_reaches = $this->website_reach->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $website_reaches ) instanceof WebsiteReach );

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
        $dt->order_by( 'name', 'wu.`email`', 'wr.`assigned_to`', 'wr.`status`', 'wr.`priority`', 'wr.`date_created`' );

        $count = $this->website_reach->count_all( $dt->get_count_variables() );

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
        $this->website_reach = null;
    }
}
