<?php

require_once 'base-database-test.php';

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
     * Get
     */
    public function testGet() {
        // Declare variables
        $text = 'bla bla bla';
        $website_id = -3;

        // Create
        $craigslist_ad_id = $this->db->insert( 'craigslist_ads', array(
            'website_id' => $website_id
            , 'text' => $text
        ), 'is' );

        // Get ad
        $this->craigslist_ad->get( $craigslist_ad_id, $website_id );

        // Compare
        $this->assertEquals( $text, $this->craigslist_ad->text );

        $this->db->delete( 'craigslist_ads', compact( 'craigslist_ad_id' ), 'i' );
    }

    /**
     * Create
     */
    public function testCreate() {
        // Declare variables
        $original_text = 'bla bla bla';
        $website_id = -3;

        // Create test
        $this->craigslist_ad->website_id = $website_id;
        $this->craigslist_ad->text = $original_text;
        $this->craigslist_ad->create();

        $this->assertTrue( !is_null( $this->craigslist_ad->id ) );

        // Get the message
        $text = $this->db->get_var( 'SELECT `text` FROM `craigslist_ads` WHERE `craigslist_ad_id` = ' . (int) $this->craigslist_ad->id );

        $this->assertEquals( $original_text, $text );

        // Delete the note
        $this->db->delete( 'craigslist_ads', compact( 'website_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     * @depends testGet
     */
    public function testSave() {
        // Declare variables
        $website_id = -5;
        $original_text = 'bla bla bla';
        $new_text = 'alb alb alb';

        // Create test
        $this->craigslist_ad->website_id = $website_id;
        $this->craigslist_ad->text = $original_text;
        $this->craigslist_ad->create();

        // Update test
        $this->craigslist_ad->text = $new_text;
        $this->craigslist_ad->save();

        // Now check it!
        $this->craigslist_ad->get( $this->craigslist_ad->id, $website_id );

        $this->assertEquals( $new_text, $this->craigslist_ad->text );

        // Delete the attribute item
        $this->db->delete( 'craigslist_ads', compact( 'website_id' ), 'i' );
    }

    /**
     * Get Markets
     */
    public function testGetMarkets() {
        
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
        $dt->order_by( 'cah.`headline`', 'ca.`text`', 'p.`name`', 'p.`sku`', 'ca.`active`', 'ca.`date_created`' );

        $craigslist_ads = $this->craigslist_ad->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $craigslist_ads ) instanceof CraigslistAd );

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
        $dt->order_by( 'cah.`headline`', 'ca.`text`', 'p.`name`', 'p.`sku`', 'ca.`active`', 'ca.`date_created`' );

        $count = $this->craigslist_ad->count_all( $dt->get_count_variables() );

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
        $this->craigslist_ad = null;
    }
}
