<?php

require_once 'test/base-database-test.php';

class SocialMediaFacebookPageTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaFacebookPage
     */
    private $sm_facebook_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_facebook_page = new SocialMediaFacebookPage();
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Get
//     */
//    public function testGet() {
//        // Declare variables
//        $id = -5;
//        $website_id = -7;
//        $name = 'Gertrude';
//
//        // Insert
//        $this->phactory->insert( 'sm_facebook_page', compact( 'id', 'website_id', 'name' ), 'iis' );
//
//        // Get
//        $this->sm_facebook_page->get( $id, $website_id );
//
//        $this->assertEquals( $name, $this->sm_facebook_page->name );
//
//        // Clean up
//        $this->phactory->delete( 'sm_facebook_page', compact( 'id' ), 'i' );
//    }
//
//    /**
//     * Test create
//     */
//    public function testCreate() {
//        // Declare variables
//        $website_id = -7;
//        $name = 'Gertrude';
//
//        // Create
//        $this->sm_facebook_page->website_id = $website_id;
//        $this->sm_facebook_page->name = $name;
//        $this->sm_facebook_page->create();
//
//        // Get
//        $retrieved_name = $this->phactory->get_var( 'SELECT `name` FROM `sm_facebook_page` WHERE `id` = ' . (int) $this->sm_facebook_page->id );
//
//        $this->assertEquals( $retrieved_name, $this->sm_facebook_page->name );
//
//        // Clean up
//        $this->phactory->delete( 'sm_facebook_page', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Save
//     *
//     * @depends testCreate
//     */
//    public function testSave() {
//        // Declare variables
//        $website_id = -7;
//        $name = 'Gertrude';
//
//        // Create
//        $this->sm_facebook_page->$website_id = $website_id;
//        $this->sm_facebook_page->create();
//
//        // Update test
//        $this->sm_facebook_page->name = $name;
//        $this->sm_facebook_page->save();
//
//        // Check it
//        $retrieved_name = $this->phactory->get_var( 'SELECT `name` FROM `sm_facebook_page` WHERE `id` = ' . (int) $this->sm_facebook_page->id );
//
//        $this->assertEquals( $retrieved_name, $this->sm_facebook_page->name );
//
//        // Clean up
//        $this->phactory->delete( 'sm_facebook_page', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * List All
//     */
//    public function testListAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( '`name`', '`date_created`' );
//
//        $sm_facebook_pages = $this->sm_facebook_page->list_all( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( current( $sm_facebook_pages ) instanceof SocialMediaFacebookPage );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $emails );
//    }
//
//    /**
//     * Count All
//     */
//    public function testCountAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( '`name`', '`date_created`' );
//
//        $count = $this->sm_facebook_page->count_all( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_facebook_page = null;
    }
}
