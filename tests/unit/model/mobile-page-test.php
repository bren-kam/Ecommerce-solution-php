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
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_page = null;
    }
}
