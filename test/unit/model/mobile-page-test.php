<?php

require_once 'test/base-database-test.php';

class MobilePageTest extends BaseDatabaseTest {
    const SLUG = 'current-offer';

    /**
     * @var MobilePage
     */
    private $mobile_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_page = new MobilePage();

        // Define
        $this->phactory->define( 'mobile_pages', array( 'website_id' => self::WEBSITE_ID, 'slug' => self::SLUG ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_mobile_page = $this->phactory->create('mobile_pages');

        // Get
        $this->mobile_page->get( $ph_mobile_page->mobile_page_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::SLUG, $this->mobile_page->slug );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->mobile_page->website_id = self::WEBSITE_ID;
        $this->mobile_page->slug = self::SLUG;
        $this->mobile_page->create();

        // Assert
        $this->assertNotNull( $this->mobile_page->id );

        // Get
        $ph_mobile_page = $this->phactory->get( 'mobile_pages', array( 'mobile_page_id' => $this->mobile_page->id ) );

        // Assert
        $this->assertEquals( self::SLUG, $ph_mobile_page->slug );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_mobile_page = $this->phactory->create('mobile_pages');

        // Save
        $this->mobile_page->id = $ph_mobile_page->mobile_page_id;
        $this->mobile_page->slug = 'about-us';
        $this->mobile_page->save();

        // Get
        $ph_mobile_page = $this->phactory->get( 'mobile_pages', array( 'mobile_page_id' => $ph_mobile_page->mobile_page_id ) );

        // Assert
        $this->assertEquals( $this->mobile_page->slug, $ph_mobile_page->slug );
    }

    /**
     * Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Create
        $ph_mobile_page = $this->phactory->create('mobile_pages');

        // Remove
        $this->mobile_page->id = $ph_mobile_page->mobile_page_id;
        $this->mobile_page->remove();

        // Get
        $ph_mobile_page = $this->phactory->get( 'mobile_pages', array( 'mobile_page_id' => $ph_mobile_page->mobile_page_id ) );

        // Assert
        $this->assertNull( $ph_mobile_page );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Get Stub
        $user = $this->getMock('User');

        // Create
        $this->phactory->create('mobile_pages');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`title`', '`status`', '`date_updated`' );

        // Get
        $mobile_pages = $this->mobile_page->list_all( $dt->get_variables() );
        $mobile_page = current( $mobile_pages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'MobilePage', $mobile_pages );
        $this->assertEquals( self::SLUG, $mobile_page->slug );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get Stub
        $user = $this->getMock('User');

        // Create
        $this->phactory->create('mobile_pages');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`title`', '`status`', '`date_updated`' );

        // Get
        $count = $this->mobile_page->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_page = null;
    }
}
