<?php

require_once 'base-database-test.php';

class CraigslistMarketLinkTest extends BaseDatabaseTest {
    /**
     * @var CraigslistMarketLink
     */
    private $craigslist_market_link;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->craigslist_market_link = new CraigslistMarketLink();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Delete before
        $this->db->delete( 'craigslist_market_links', array( 'website_id' => -1 ), 'i' );

        // Create
        $this->craigslist_market_link->website_id = -1;
        $this->craigslist_market_link->craigslist_market_id = -2;
        $this->craigslist_market_link->market_id = -3;
        $this->craigslist_market_link->cl_category_id = -4;
        $this->craigslist_market_link->create();

        // Try to get something
        $cl_category_id = $this->db->get_var( "SELECT `cl_category_id` FROM `craigslist_market_links` WHERE `website_id` = -1 AND `craigslist_market_id` = -2 AND `market_id` = -3" );

        $this->assertEquals( '-4', $cl_category_id );

        // Now delete it
        $this->db->delete( 'craigslist_market_links', array( 'website_id' => -1 ), 'i' );
    }
    /**
     * Test Getting all
     *
     * @depends testCreate
     */
    public function testGetByAccount() {
        // Declare variables
        $account_id = 160;

        // Now get them
        $craigslist_markets = $this->craigslist_market_link->get_by_account( $account_id );

        $this->assertTrue( current( $craigslist_markets ) instanceof CraigslistMarketLink );
    }

    /**
     * Test getting cl_category_ids by account
     *
     * @depends testCreate
     */
    public function testGetClCategoryIdsByAccount() {
        // Declare variables
        $account_id = 160;
        $cl_market_id = 382;

        // Get the IDs
        $cl_category_ids = $this->craigslist_market_link->get_cl_category_ids_by_account( $account_id, $cl_market_id );

        $this->assertEquals( array( '2086' ), $cl_category_ids );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->craigslist_market_link = null;
    }
}
