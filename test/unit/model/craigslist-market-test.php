<?php

require_once 'test/base-database-test.php';

class CraigslistMarketTest extends BaseDatabaseTest {
    /**
     * @var CraigslistMarket
     */
    private $craigslist_market;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->craigslist_market = new CraigslistMarket();
    }

    /**
     * Test getting the company
     */
    public function testGet() {
        // Declare variables
        $craigslist_market_id = 1;

        // Get company
        $this->craigslist_market->get( $craigslist_market_id );

        $this->assertEquals( 'Lexington', $this->craigslist_market->city );
    }

    /**
     * Test Getting all
     */
    public function testGetAll() {
        $craigslist_markets = $this->craigslist_market->get_all();

        $this->assertTrue( current( $craigslist_markets ) instanceof CraigslistMarket );
    }

    /**
     * Test Get By Ad
     */
    public function testGetByAd() {
        // Declare variables
        $website_id = -5;
        $craigslist_ad_id = -3;
        $craigslist_market_id = 1;

        // Insert
        $this->phactory->insert( 'craigslist_ad_markets', compact( 'craigslist_ad_id', 'craigslist_market_id' ), 'ii' );
        $this->phactory->insert( 'craigslist_market_links', compact( 'craigslist_market_id', 'website_id' ), 'ii' );

        $craigslist_markets = $this->craigslist_market->get_by_ad( $craigslist_ad_id, $website_id );

        $this->assertTrue( current( $craigslist_markets ) instanceof CraigslistMarket );

        // Cleanup
        $this->phactory->delete( 'craigslist_ad_markets', compact( 'craigslist_ad_id' ), 'i' );
        $this->phactory->delete( 'craigslist_market_links', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Get By Ad
     */
    public function testGetByAccount() {
        // Declare variables
        $website_id = -5;
        $craigslist_market_id = 1;

        // Insert
        $this->phactory->insert( 'craigslist_market_links', compact( 'craigslist_market_id', 'website_id' ), 'ii' );

        $craigslist_markets = $this->craigslist_market->get_by_account( $website_id );

        $this->assertTrue( current( $craigslist_markets ) instanceof CraigslistMarket );

        // Cleanup
        $this->phactory->delete( 'craigslist_market_links', compact( 'website_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->craigslist_market = null;
    }
}
