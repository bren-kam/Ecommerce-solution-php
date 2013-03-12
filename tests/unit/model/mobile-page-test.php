<?php

require_once 'base-database-test.php';

class MobilePageTest extends BaseDatabaseTest {
    /**
     * @var MobilePage
     */
    private $mobile_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_page = new MobilePage();
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->mobile_page->website_id = -3;
        $this->mobile_page->slug = 'enders-game';
        $this->mobile_page->title = "Ender's Game";
        $this->mobile_page->create();

        $this->assertTrue( !is_null( $this->mobile_page->id ) );

        // Make sure it's in the database
        $slug = $this->db->get_var( 'SELECT `slug` FROM `mobile_pages` WHERE `mobile_page_id` = ' . (int) $this->mobile_page->id );

        $this->assertEquals( 'enders-game', $slug );

        // Delete
        $this->db->delete( 'mobile_pages', array( 'mobile_page_id' => $this->mobile_page->id ), 'i' );
    }
    
    /**
     * Save
     */
    public function testSave() {
        // Declare variables
        $slug = 'originla-slug';
        $new_slug = 'original-slug';

        // Create posting post
        $this->mobile_page->id = $this->db->insert( 'mobile_pages', compact( 'slug' ), 's' );
    
        // Update test
        $this->mobile_page->slug = $new_slug;
        $this->mobile_page->save();
    
        $retrieved_slug = $this->db->get_var( 'SELECT `slug` FROM `mobile_pages` WHERE `mobile_page_id` = ' . (int) $this->mobile_page->id );
    
        $this->assertEquals( $retrieved_slug, $new_slug );
    
        // Delete
        $this->db->delete( 'mobile_pages', array( 'mobile_page_id' => $this->mobile_page->id ), 'i' );
    }
    
    /**
     * Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Declare variables
        $slug = 'special-offer';

        // Create
        $this->mobile_page->slug = $slug;
        $this->mobile_page->create();

        // Remove/Delete
        $this->mobile_page->remove();

        $retrieved_slug = $this->db->get_var( 'SELECT `slug` FROM `mobile_pages` WHERE `mobile_page_id` = ' . (int) $this->mobile_page->id );

        $this->assertFalse( $retrieved_slug );
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
        $dt->order_by( '`title`', '`status`', '`date_updated`' );

        $mobile_pages = $this->mobile_page->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $mobile_pages ) instanceof MobilePage );

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
        $dt->order_by( '`title`', '`status`', '`date_updated`' );

        $count = $this->mobile_page->count_all( $dt->get_count_variables() );

        // Make sure they exist
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
