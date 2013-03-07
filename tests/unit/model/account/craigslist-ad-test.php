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
