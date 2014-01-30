<?php

require_once 'test/base-database-test.php';

class CraigslistMarketLinkTest extends BaseDatabaseTest {
    const MARKET_ID = 3;
    const CL_CATEGORY_ID = 5;
    const CRAIGSLIST_MARKET_ID = 7;

    // Craigslist Markets
    const CL_MARKET_ID = 19;

    /**
     * @var CraigslistMarketLink
     */
    private $craigslist_market_link;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->craigslist_market_link = new CraigslistMarketLink();

        // Define
        $this->phactory->define( 'craigslist_market_links', array( 'website_id' => self::WEBSITE_ID, 'craigslist_market_id' => self::CRAIGSLIST_MARKET_ID, 'market_id' => self::MARKET_ID, 'cl_category_id' => self::CL_CATEGORY_ID ) );
        $this->phactory->define( 'craigslist_markets', array( 'craigslist_market_id' => self::CRAIGSLIST_MARKET_ID, 'cl_market_id' => self::CL_MARKET_ID, 'status' => CraigslistMarket::STATUS_ACTIVE ) );
        $this->phactory->recall();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->craigslist_market_link->website_id = self::WEBSITE_ID;
        $this->craigslist_market_link->craigslist_market_id = self::CRAIGSLIST_MARKET_ID;
        $this->craigslist_market_link->create();

        // Try to get something
        $ph_craigslist_market_link = $this->phactory->get( 'craigslist_market_links', array( 'website_id' => self::WEBSITE_ID ) );

        $this->assertEquals( self::CRAIGSLIST_MARKET_ID, $ph_craigslist_market_link->craigslist_market_id );
    }

    /**
     * Test Getting all
     *
     * @depends testCreate
     */
    public function testGetByAccount() {
        // Create
        $this->phactory->create('craigslist_market_links');
        $this->phactory->create('craigslist_markets');

        // Get
        $craigslist_markets = $this->craigslist_market_link->get_by_account( self::WEBSITE_ID );
        $craigslist_market = current( $craigslist_markets );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'CraigslistMarketLink', $craigslist_markets );
        $this->assertEquals( self::MARKET_ID, $craigslist_market->market_id );
    }

    /**
     * Test getting cl_category_ids by account
     *
     * @depends testCreate
     */
    public function testGetClCategoryIdsByAccount() {
        // Create
        $this->phactory->create('craigslist_market_links');
        $this->phactory->create('craigslist_markets');

        // Get the IDs
        $cl_category_ids = $this->craigslist_market_link->get_cl_category_ids_by_account( self::WEBSITE_ID, self::CL_MARKET_ID );
        $expected_array = array ( self::CL_CATEGORY_ID );

        // Assert
        $this->assertEquals( $expected_array, $cl_category_ids );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->craigslist_market_link = null;
    }
}
