<?php

require_once 'base-database-test.php';

class AccountPagemetaTest extends BaseDatabaseTest {
    /**
     * @var AccountPagemeta
     */
    private $account_pagemeta;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_pagemeta = new AccountPagemeta();
    }

    /**
     * Test get by keys
     */
    public function testGetByAccountAndKeys() {
        // Declare variables
        $account_id = 96;
        $key_1 = 'display-coupon';
        $key_2 = 'email';

        // Get
        $pagemeta = $this->account_pagemeta->get_by_account_and_keys( $account_id, $key_1, $key_2 );

        $this->assertEquals( 2, count( $pagemeta ) );
    }

    /**
     * Test get by keys
     */
    public function testGetByKeys() {
        // Declare variables
        $account_page_id = 7;

        // Get
        $pagemeta = $this->account_pagemeta->get_by_keys( $account_page_id, 'display-coupon', 'email' );

        $this->assertTrue( is_array( $pagemeta ) );
        $this->assertEquals( count( $pagemeta ), 2 );
    }

    /**
     * Test get by keys
     */
    public function testGetForPagesByKeys() {
        // Declare variables
        $account_page_ids = array( 7, 8 );
        $account_pagemeta_keys = array( 'display-coupon', 'apply-now' );

        // Get
        $pagemeta = $this->account_pagemeta->get_for_pages_by_keys( $account_page_ids, $account_pagemeta_keys );

        $this->assertTrue( current( $pagemeta ) instanceof AccountPagemeta );
        $this->assertEquals( count( $pagemeta ), 2 );
    }

    /**
     * Test Add Bulk
     */
    public function testAddBulk() {
        // Declare variable
        $pagemeta = array(
            array(
                'website_page_id' => -1
                , 'key' => 'beans'
                , 'value' => 'black'
            )
            , array(
                'website_page_id' => -2
                , 'key' => 'skittles'
                , 'value' => 'sour'
            )
        );

        // Delete anything before hand
        $this->phactory->query( 'DELETE FROM `website_pagemeta` WHERE `website_page_id` IN ( -1, -2 )' );

        // Add them
        $this->account_pagemeta->add_bulk( $pagemeta );

        // Get them
        $fetched_pagemeta = ar::assign_key( $this->phactory->get_results( 'SELECT * FROM `website_pagemeta` WHERE `website_page_id` IN( -1, -2 )', PDO::FETCH_ASSOC ), 'website_page_id' );

        $this->assertEquals( count( $fetched_pagemeta ), 2 );
        $this->assertEquals( $fetched_pagemeta[-2]['key'], 'skittles' );

        // Delete
        $this->phactory->query( 'DELETE FROM `website_pagemeta` WHERE `website_page_id` IN ( -1, -2 )' );
    }

    /**
     * Test Add Bulk
     */
    public function testAddBulkByPage() {
        // Declare variables
        $website_page_id = -3;
        $pagemeta = array(
            'beans '=> 'pinto'
            , 'skittles' => 'bitter'
        );

        // Add them
        $this->account_pagemeta->add_bulk_by_page( $website_page_id, $pagemeta );

        // Get them
        $fetched_pagemeta = ar::assign_key( $this->phactory->get_results( "SELECT * FROM `website_pagemeta` WHERE `website_page_id` = $website_page_id", PDO::FETCH_ASSOC ), 'key', true );

        $this->assertEquals( $fetched_pagemeta['skittles']['value'], 'bitter' );

        // Delete
        $this->phactory->delete( 'website_pagemeta', compact( 'website_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_pagemeta = null;
    }
}
