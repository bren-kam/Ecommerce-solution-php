<?php

require_once 'test/base-database-test.php';

class CraigslistAdTest extends BaseDatabaseTest {
    /**
     * @var CraigslistAd
     */
    private $craigslist_ad;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->craigslist_ad = new CraigslistAd();
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Get
//     */
//    public function testGet() {
//        // Declare variables
//        $text = 'bla bla bla';
//        $website_id = -3;
//
//        // Create
//        $craigslist_ad_id = $this->phactory->insert( 'craigslist_ads', array(
//            'website_id' => $website_id
//            , 'text' => $text
//        ), 'is' );
//
//        // Get ad
//        $this->craigslist_ad->get( $craigslist_ad_id, $website_id );
//
//        // Compare
//        $this->assertEquals( $text, $this->craigslist_ad->text );
//
//        $this->phactory->delete( 'craigslist_ads', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Get Markets
//     */
//    public function testGetMarkets() {
//        // Test protected method
//        $class = new ReflectionClass('CraigslistAd');
//        $method = $class->getMethod( 'get_markets' );
//        $method->setAccessible(true);
//
//        // Set variables
//        $craigslist_ad_id = -3;
//        $craigslist_market_id = -5;
//
//        // Insert
//        $this->phactory->insert( 'craigslist_ad_markets', compact( 'craigslist_ad_id', 'craigslist_market_id'  ), 'ii' );
//
//        // Assign id
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Get markets
//        $craigslist_market_ids = $method->invoke( $this->craigslist_ad );
//
//        $this->assertEquals( array( $craigslist_market_id ), $craigslist_market_ids );
//
//        // Delete
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Get
//     *
//     * @depends testGet
//     * @depends testGetMarkets
//     */
//    public function testGetComplete() {
//        // Declare variables
//        $text = 'bla bla bla';
//        $website_id = -3;
//        $craigslist_market_id = -7;
//
//        // Create
//        $craigslist_ad_id = $this->phactory->insert( 'craigslist_ads', array(
//            'website_id' => $website_id
//            , 'text' => $text
//        ), 'is' );
//
//        // Insert
//        $this->phactory->insert( 'craigslist_ad_markets', compact( 'craigslist_ad_id', 'craigslist_market_id'  ), 'ii' );
//
//        // Get ad
//        $this->craigslist_ad->get_complete( $craigslist_ad_id, $website_id );
//
//        // Compare
//        $this->assertEquals( $text, $this->craigslist_ad->text );
//        $this->assertEquals( array( $craigslist_market_id ), $this->craigslist_ad->craigslist_markets );
//
//        $this->phactory->delete( 'craigslist_ads', compact( 'craigslist_ad_id' ), 'i' );
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Create
//     */
//    public function testCreate() {
//        // Declare variables
//        $original_text = 'bla bla bla';
//        $website_id = -3;
//
//        // Create test
//        $this->craigslist_ad->website_id = $website_id;
//        $this->craigslist_ad->text = $original_text;
//        $this->craigslist_ad->create();
//
//        $this->assertNotNull( $this->craigslist_ad->id ) );
//
//        // Get the message
//        $text = $this->phactory->get_var( 'SELECT `text` FROM `craigslist_ads` WHERE `craigslist_ad_id` = ' . (int) $this->craigslist_ad->id );
//
//        $this->assertEquals( $original_text, $text );
//
//        // Delete the note
//        $this->phactory->delete( 'craigslist_ads', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Save
//     *
//     * @depends testCreate
//     * @depends testGet
//     */
//    public function testSave() {
//        // Declare variables
//        $website_id = -5;
//        $original_text = 'bla bla bla';
//        $new_text = 'alb alb alb';
//
//        // Create test
//        $this->craigslist_ad->website_id = $website_id;
//        $this->craigslist_ad->text = $original_text;
//        $this->craigslist_ad->create();
//
//        // Update test
//        $this->craigslist_ad->text = $new_text;
//        $this->craigslist_ad->save();
//
//        // Now check it!
//        $this->craigslist_ad->get( $this->craigslist_ad->id, $website_id );
//
//        $this->assertEquals( $new_text, $this->craigslist_ad->text );
//
//        // Delete the attribute item
//        $this->phactory->delete( 'craigslist_ads', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Add Headlines
//     */
//    public function testAddHeadlines() {
//        // Declare variables
//        $craigslist_ad_id = -5;
//        $headlines = array( 'test', 'test2', 'test3' );
//
//        // Set the id
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Add headlines
//        $this->craigslist_ad->add_headlines( $headlines );
//
//        // Get headlines
//        $retrieved_headlines = $this->phactory->get_col( "SELECT `headline` FROM `craigslist_ad_headlines` WHERE `craigslist_ad_id` = $craigslist_ad_id ORDER BY `headline` ASC" );
//
//        $this->assertEquals( $headlines, $retrieved_headlines );
//
//        // Clean up
//        $this->phactory->delete( 'craigslist_ad_headlines', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Delete Headlines
//     *
//     * @depends testAddHeadlines
//     */
//    public function testDeleteHeadlines() {
//        // Declare variables
//        $craigslist_ad_id = -5;
//        $headlines = array( 'test', 'test2', 'test3' );
//
//        // Set the id
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Add headlines
//        $this->craigslist_ad->add_headlines( $headlines );
//
//        // Remove them
//        $this->craigslist_ad->delete_headlines();
//
//        // Get headlines
//        $retrieved_headlines = $this->phactory->get_col( "SELECT `headline` FROM `craigslist_ad_headlines` WHERE `craigslist_ad_id` = $craigslist_ad_id" );
//
//        $this->assertTrue( empty( $retrieved_headlines ) );
//    }
//
//    /**
//     * Add Markets
//     */
//    public function testAddMarkets() {
//        // Test protected method
//        $class = new ReflectionClass('CraigslistAd');
//        $method = $class->getMethod( 'add_markets' );
//        $method->setAccessible(true);
//
//        // Declare variables
//        $craigslist_ad_id = -5;
//        $craigslist_market_ids = array( -11, -12, -13 );
//
//        // Set the id
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Add
//        $method->invokeArgs( $this->craigslist_ad, array( $craigslist_market_ids ) );
//
//        // Get
//        $retrieved_craigslist_market_ids = $this->phactory->get_col( "SELECT `craigslist_market_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id ORDER BY `craigslist_market_id` DESC" );
//
//        $this->assertEquals( $craigslist_market_ids, $retrieved_craigslist_market_ids );
//
//        // Clean up
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Delete Markets
//     */
//    public function testDeleteMarkets() {
//        // Test protected method
//        $class = new ReflectionClass('CraigslistAd');
//        $method = $class->getMethod( 'delete_markets' );
//        $method->setAccessible(true);
//
//        // Declare variables
//        $craigslist_ad_id = -5;
//        $craigslist_market_ids = array( -7 );
//
//        // Set the id
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Insert them
//        $this->phactory->query( "INSERT INTO `craigslist_ad_markets` ( `craigslist_ad_id`, `craigslist_market_id` ) VALUES ( $craigslist_ad_id, -7 ), ( $craigslist_ad_id, -8 ), ( $craigslist_ad_id, -9 ) ON DUPLICATE KEY UPDATE `craigslist_ad_id` = VALUES( `craigslist_ad_id` )" );
//
//        // Delete
//        $method->invokeArgs( $this->craigslist_ad, array( $craigslist_market_ids ) );
//
//        $retrieved_craigslist_market_ids = $this->phactory->get_col( "SELECT `craigslist_market_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id" );
//
//        $this->assertEquals( $craigslist_market_ids, $retrieved_craigslist_market_ids );
//    }
//
//    /**
//     * Set Markets
//     *
//     * @depends testAddMarkets
//     * @depends testDeleteMarkets
//     */
//    public function testSetMarkets() {
//        // Declare variables
//        $craigslist_ad_id = -5;
//        $craigslist_market_ids = array( -7, -8, -9 );
//        $set_craigslist_market_ids = array( -11, -12, -13 );
//
//        // Set the internal variables
//        $this->craigslist_ad->id = $craigslist_ad_id;
//        $this->craigslist_ad->craigslist_markets = $craigslist_market_ids;
//
//        // Insert other ones (which should be deleted
//        $this->phactory->query( "INSERT INTO `craigslist_ad_markets` ( `craigslist_ad_id`, `craigslist_market_id` ) VALUES ( $craigslist_ad_id, -7 ), ( $craigslist_ad_id, -8 ), ( $craigslist_ad_id, -9 ) ON DUPLICATE KEY UPDATE `craigslist_ad_id` = VALUES( `craigslist_ad_id` )" );
//
//        // Set markets
//        $this->craigslist_ad->set_markets( $set_craigslist_market_ids );
//
//        // Get
//        $retrieved_craigslist_market_ids = $this->phactory->get_col( "SELECT `craigslist_market_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id ORDER BY `craigslist_market_id` DESC" );
//
//        $this->assertEquals( $set_craigslist_market_ids, $retrieved_craigslist_market_ids );
//
//        // Clean up
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * List All
//     */
//    public function testListAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'cah.`headline`', 'ca.`text`', 'p.`name`', 'p.`sku`', 'ca.`active`', 'ca.`date_created`' );
//
//        $craigslist_ads = $this->craigslist_ad->list_all( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( current( $craigslist_ads ) instanceof CraigslistAd );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $emails );
//    }
//
//    /**
//     * Count All
//     */
//    public function testCountAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'cah.`headline`', 'ca.`text`', 'p.`name`', 'p.`sku`', 'ca.`active`', 'ca.`date_created`' );
//
//        $count = $this->craigslist_ad->count_all( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }
//
//    /**
//     * Get Primus Product IDs
//     */
//    public function testGetPrimusProductIds() {
//        // Access protected method
//        $class = new ReflectionClass('CraigslistAd');
//        $method = $class->getMethod('get_primus_product_ids');
//        $method->setAccessible(true);
//
//        // Declare variables
//        $craigslist_ad_id = -9;
//        $primus_product_id = -7;
//
//        // Set ID
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Insert
//        $this->phactory->insert( 'craigslist_ad_markets', compact( 'craigslist_ad_id', 'primus_product_id' ), 'ii' );
//
//        // Get
//        $retrieved_primus_product_ids = $method->invoke( $this->craigslist_ad );
//
//        $this->assertEquals( array( $primus_product_id ), $retrieved_primus_product_ids );
//
//        // Clean up
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Remove Primus Product Ids
//     */
//    public function testRemovePrimusProductIds() {
//        // Access protected method
//        $class = new ReflectionClass('CraigslistAd');
//        $method = $class->getMethod('remove_primus_product_ids');
//        $method->setAccessible(true);
//
//        // Declare variables
//        $craigslist_ad_id = -11;
//        $primus_product_id = -7;
//
//        // Set ID
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Insert
//        $this->phactory->insert( 'craigslist_ad_markets', compact( 'craigslist_ad_id', 'primus_product_id' ), 'ii' );
//
//        // Remove
//        $method->invoke( $this->craigslist_ad );
//
//        // Get primus product id
//        $retrieved_primus_product_id = $this->phactory->get_var( "SELECT `primus_product_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id" );
//
//        $this->assertEquals( 0, $retrieved_primus_product_id );
//
//        // Clean up
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }
//
//    /**
//     * Delete From Primus
//     *
//     * @depends testCreate
//     * @depends testSave
//     * @depends testGetPrimusProductIds
//     * @depends testRemovePrimusProductIds
//     */
//    public function testDeleteFromPrimus() {
//        // Declare variables
//        $primus_product_id = -7;
//        $website_id = -5;
//        $blank_date = '0000-00-00 00:00:00';
//
//        // Create stub
//        library( 'craigslist-api' );
//        $stub_craigslist = $this->getMock( 'Craigslist_API', array(), array(), '', false );
//        $stub_craigslist->expects($this->once())->method('delete_ad_product')->will($this->returnValue(true));
//
//        // Create and set date posted
//        $this->craigslist_ad->website_id = $website_id;
//        $this->craigslist_ad->create();
//
//        $this->craigslist_ad->date_posted = '2013-03-07 13:21:00';
//        $this->craigslist_ad->save();
//
//        // Insert ad markets
//        $this->phactory->insert( 'craigslist_ad_markets', array( 'craigslist_ad_id' => $this->craigslist_ad->id, 'primus_product_id' => $primus_product_id ), 'ii' );
//
//        // Do it
//        $this->craigslist_ad->delete_from_primus( $stub_craigslist );
//
//        // Test
//        $this->assertEquals( $blank_date, $this->craigslist_ad->date_posted );
//
//        // Test primus product id
//        $retrieved_primus_product_id = $this->phactory->get_var( "SELECT `primus_product_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = " . (int) $this->craigslist_ad->id );
//
//        $this->assertEquals( 0, $retrieved_primus_product_id );
//
//        // Clean up
//        $this->phactory->delete( 'craigslist_ad_markets', array( 'craigslist_ad_id' => $this->craigslist_ad->id ), 'i' );
//        $this->phactory->delete( 'craigslist_ads', array( 'craigslist_ad_id' => $this->craigslist_ad->id ), 'i' );
//    }
//
//    /**
//     * Set Craigslist Ad Markets
//     */
//    public function testSetCraigslistAdMarkets() {
//        // Get protected method
//        $class = new ReflectionClass('CraigslistAd');
//        $method = $class->getMethod('set_craigslist_ad_markets');
//        $method->setAccessible(true);
//
//        // Declare variables
//        $craigslist_ad_id = -7;
//        $craigslist_market_ads = array(
//            -2 => -12
//            , -4 => -14
//            , -6 => -16
//        );
//
//        // Set the ID
//        $this->craigslist_ad->id = $craigslist_ad_id;
//
//        // Insert a few craigslist_market_ads
//        $this->phactory->query( "INSERT INTO `craigslist_ad_markets` ( `craigslist_ad_id`, `craigslist_market_id` ) VALUES ( $craigslist_ad_id, -2 ), ( $craigslist_ad_id, -4 ), ( $craigslist_ad_id, -6 )" );
//
//        // Set primus ads
//        $method->invokeArgs( $this->craigslist_ad, array( $craigslist_market_ads ) );
//
//        // Get primus product_ids
//        $primus_product_ids = $this->phactory->get_col( "SELECT `primus_product_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id ORDER BY `primus_product_id` DESC" );
//
//        // Make sure they're equal
//        $this->assertEquals( array_values( $craigslist_market_ads ), $primus_product_ids );
//
//        // Clean Up
//        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->craigslist_ad = null;
    }
}
