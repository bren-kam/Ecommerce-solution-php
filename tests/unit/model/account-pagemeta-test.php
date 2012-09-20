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
    public function testGetByKeys() {
        // Declare variables
        $account_page_ids = array( 7, 8 );
        $account_pagemeta_keys = array( 'display-coupon', 'apply-now' );

        // Get
        $pagemeta = $this->account_pagemeta->get_by_keys( $account_page_ids, $account_pagemeta_keys );

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
        $this->db->query( 'DELETE FROM `website_pagemeta` WHERE `website_page_id` IN ( -1, -2 )' );

        // Add them
        $this->account_pagemeta->add_bulk( $pagemeta );

        // Get them
        $fetched_pagemeta = ar::assign_key( $this->db->get_results( 'SELECT * FROM `website_pagemeta` WHERE `website_page_id` IN( -1, -2 )', PDO::FETCH_ASSOC ), 'website_page_id' );

        $this->assertEquals( count( $fetched_pagemeta ), 2 );
        $this->assertEquals( $fetched_pagemeta[-2]['key'], 'skittles' );

        // Delete
        $this->db->query( 'DELETE FROM `website_pagemeta` WHERE `website_page_id` IN ( -1, -2 )' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_pagemeta = null;
    }
}
