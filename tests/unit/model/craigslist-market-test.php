<?php

require_once 'base-database-test.php';

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
     * Will be executed after every test
     */
    public function tearDown() {
        $this->craigslist_market = null;
    }
}
