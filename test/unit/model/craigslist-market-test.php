<?php

require_once 'test/base-database-test.php';

class CraigslistMarketTest extends BaseDatabaseTest {
    const CRAIGSLIST_MARKET_ID = 7;
    const CL_MARKET_ID = 9;

    // Craigslist Ad Markets
    const CRAIGSLIST_AD_ID = 15;

    /**
     * @var CraigslistMarket
     */
    private $craigslist_market;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->craigslist_market = new CraigslistMarket();

        // Define
        $this->phactory->define( 'craigslist_markets', array( 'craigslist_market_id' => self::CRAIGSLIST_MARKET_ID, 'cl_market_id' => self::CL_MARKET_ID, 'status' => CraigslistMarket::STATUS_ACTIVE ) );
        $this->phactory->define( 'craigslist_ad_markets', array( 'craigslist_ad_id' => self::CRAIGSLIST_AD_ID, 'craigslist_market_id' => self::CRAIGSLIST_MARKET_ID ) );
        $this->phactory->define( 'craigslist_market_links', array( 'website_id' => self::WEBSITE_ID, 'craigslist_market_id' => self::CRAIGSLIST_MARKET_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test getting the company
     */
    public function testGet() {
        // Create
        $this->phactory->create('craigslist_markets');

        // Get company
        $this->craigslist_market->get( self::CRAIGSLIST_MARKET_ID );

        // Assert
        $this->assertEquals( self::CL_MARKET_ID, $this->craigslist_market->cl_market_id );
    }

    /**
     * Test Getting all
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('craigslist_markets');

        // Get
        $craigslist_markets = $this->craigslist_market->get_all();
        $craigslist_market = current( $craigslist_markets );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'CraigslistMarket', $craigslist_markets );
        $this->assertEquals( self::CRAIGSLIST_MARKET_ID, $craigslist_market->craigslist_market_id );
    }

    /**
     * Test Get By Ad
     */
    public function testGetByAd() {
        // Create
        $this->phactory->create('craigslist_markets');
        $this->phactory->create('craigslist_ad_markets');
        $this->phactory->create('craigslist_market_links');

        // Get
        $craigslist_markets = $this->craigslist_market->get_by_ad( self::CRAIGSLIST_AD_ID, self::WEBSITE_ID );
        $craigslist_market = current( $craigslist_markets );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'CraigslistMarket', $craigslist_markets );
        $this->assertEquals( self::CRAIGSLIST_MARKET_ID, $craigslist_market->craigslist_market_id );
    }

    /**
     * Test Get By Ad
     */
    public function testGetByAccount() {
        // Create
        $this->phactory->create('craigslist_markets');
        $this->phactory->create('craigslist_market_links');

        // Get
        $craigslist_markets = $this->craigslist_market->get_by_account( self::WEBSITE_ID );
        $craigslist_market = current( $craigslist_markets );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'CraigslistMarket', $craigslist_markets );
        $this->assertEquals( self::CRAIGSLIST_MARKET_ID, $craigslist_market->craigslist_market_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->craigslist_market = null;
    }
}
