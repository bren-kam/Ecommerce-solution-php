<?php

require_once 'base-database-test.php';

class AccountPagemetaTest extends BaseDatabaseTest {
    const WEBSITE_PAGE_ID = 5;
    const KEY = 'email';
    const KEY_2 = 'display_coupon';
    const VALUE = 'bobloblob@law.com';

    /**
     * @var AccountPagemeta
     */
    private $account_pagemeta;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_pagemeta = new AccountPagemeta();

        // Define
        $this->phactory->define( 'website_pagemeta', array( 'website_page_id' => self::WEBSITE_PAGE_ID, 'key' => self::KEY, 'value' => self::VALUE ) );
        $this->phactory->define( 'website_pages', array( 'website_id' => self::WEBSITE_ID, 'website_page_id' => self::WEBSITE_PAGE_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test get by keys
     */
    public function testGetByAccountAndKeys() {
        // Create
        $this->phactory->create( 'website_pages' );
        $this->phactory->create( 'website_pagemeta' );
        $this->phactory->create( 'website_pagemeta', array( 'key' => self::KEY_2 ) );

        // Get 1
        $pagemeta = $this->account_pagemeta->get_by_account_and_keys( self::WEBSITE_ID, self::KEY );

        $this->assertEquals( self::VALUE, $pagemeta );

        // Get 2
        $pagemeta = $this->account_pagemeta->get_by_account_and_keys( self::WEBSITE_ID, self::KEY, self::KEY_2 );
        $expected_array = array( self::KEY => self::VALUE, self::KEY_2 => self::VALUE );

        $this->assertEquals( $expected_array, $pagemeta );
    }

    /**
     * Test get by keys
     */
    public function testGetForPagesByKeys() {
        // Create
        $this->phactory->create( 'website_pages' );
        $this->phactory->create( 'website_pagemeta' );
        $this->phactory->create( 'website_pagemeta', array( 'key' => self::KEY_2 ) );

        // Get
        $pagemeta = $this->account_pagemeta->get_for_pages_by_keys( array( self::WEBSITE_PAGE_ID ), array( self::KEY, self::KEY_2 ) );
        $pagemeton = current( $pagemeta );

        $this->assertContainsOnlyInstancesOf( 'AccountPagemeta', $pagemeta );
        $this->assertEquals( self::VALUE, $pagemeton->value );
    }

    /**
     * Test get by keys
     *
     * @depends testGetForPagesByKeys
     */
    public function testGetByKeys() {
        // Create
        $this->phactory->create( 'website_pages' );
        $this->phactory->create( 'website_pagemeta' );
        $this->phactory->create( 'website_pagemeta', array( 'key' => self::KEY_2 ) );

        // Get
        $pagemeta = $this->account_pagemeta->get_by_keys( self::WEBSITE_PAGE_ID, self::KEY, self::KEY_2 );
        $expected_array = array( self::KEY => self::VALUE, self::KEY_2 => self::VALUE );

        $this->assertEquals( $expected_array, $pagemeta );
    }

    /**
     * Test Add Bulk
     */
    public function testAddBulk() {
        // Reset everything
        $this->phactory->recall();

        // Declare variable
        $pagemeta = array(
            array(
                'website_page_id' => self::WEBSITE_PAGE_ID
                , 'key' => self::KEY
                , 'value' => self::VALUE
            )
            , array(
                'website_page_id' => self::WEBSITE_PAGE_ID
                , 'key' => self::KEY_2
                , 'value' => self::VALUE
            )
        );

        // Add them
        $this->account_pagemeta->add_bulk( $pagemeta );

        $ph_website_pagemeta = $this->phactory->get( 'website_pagemeta', array( 'website_page_id' => self::WEBSITE_PAGE_ID ) );
        $this->assertEquals( self::VALUE, $ph_website_pagemeta->value );
    }

    /**
     * Test Add Bulk
     */
    public function testAddBulkByPage() {
        // Reset
        $this->phactory->recall();

        // Declare variables
        $pagemeta = array(
            self::KEY => self::VALUE
            , self::KEY_2 => self::VALUE
        );

        // Add them
        $this->account_pagemeta->add_bulk_by_page( self::WEBSITE_PAGE_ID, $pagemeta );

        $ph_website_pagemeta = $this->phactory->get( 'website_pagemeta', array( 'website_page_id' => self::WEBSITE_PAGE_ID ) );
        $this->assertEquals( self::VALUE, $ph_website_pagemeta->value );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_pagemeta = null;
    }
}
