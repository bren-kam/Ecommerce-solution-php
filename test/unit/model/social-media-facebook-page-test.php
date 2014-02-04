<?php

require_once 'test/base-database-test.php';

class SocialMediaFacebookPageTest extends BaseDatabaseTest {
    const NAME = 'WhoBalloo Facebook Furniture';

    /**
     * @var SocialMediaFacebookPage
     */
    private $sm_facebook_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->sm_facebook_page = new SocialMediaFacebookPage();

        // Define
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
    }


    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_facebook_page = $this->phactory->create('sm_facebook_page');

        // Get
        $this->sm_facebook_page->get( $ph_facebook_page->id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->sm_facebook_page->name );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_facebook_page->website_id = self::WEBSITE_ID;
        $this->sm_facebook_page->name = self::NAME;
        $this->sm_facebook_page->create();

        // Assert
        $this->assertNotNull( $this->sm_facebook_page->id );

        // Get
        $ph_sm_facebook_page = $this->phactory->get( 'sm_facebook_page', array( 'id' => $this->sm_facebook_page->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_sm_facebook_page->name );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_facebook_page = $this->phactory->create('sm_facebook_page');

        // Save
        $this->sm_facebook_page->id = $ph_facebook_page->id;
        $this->sm_facebook_page->name = 'Wycolu Mattresses';
        $this->sm_facebook_page->save();

        // Get
        $ph_sm_facebook_page = $this->phactory->get( 'sm_facebook_page', array( 'id' => $ph_facebook_page->id ) );

        // Assert
        $this->assertEquals( $this->sm_facebook_page->name, $ph_sm_facebook_page->name );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('sm_facebook_page');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`date_created`' );

        // Get
        $sm_facebook_pages = $this->sm_facebook_page->list_all( $dt->get_variables() );
        $sm_facebook_page = current( $sm_facebook_pages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'SocialMediaFacebookPage', $sm_facebook_pages );
        $this->assertEquals( self::NAME, $sm_facebook_page->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('sm_facebook_page');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`date_created`' );

        // Get
        $count = $this->sm_facebook_page->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->sm_facebook_page = null;
    }
}
