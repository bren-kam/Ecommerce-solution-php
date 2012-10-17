<?php

require_once 'base-database-test.php';

class ChecklistWebsiteItemTest extends BaseDatabaseTest {
    /**
     * @var ChecklistWebsiteItem
     */
    private $checklist_website_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_website_item = new ChecklistWebsiteItem();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $checklist_website_item_id = 3;

        $this->checklist_website_item->get( $checklist_website_item_id );

        $this->assertEquals( $this->checklist_website_item->checklist_item_id, 2 );
    }

    /**
     * Test update
     *
     * @depends testGet
     */
    public function testUpdate() {
        // Declare variables
        $checklist_website_item_id = 4;
        $now = dt::now();

        // Get
        $this->checklist_website_item->get( $checklist_website_item_id );

        // Update
        $this->checklist_website_item->date_checked = $now;
        $this->checklist_website_item->update();

        // Now check it!
        $date_checked = $this->db->get_var( 'SELECT `date_checked` FROM `checklist_website_items` WHERE `checklist_website_item_id` = ' . (int) $this->checklist_website_item->id );

        $this->assertEquals( $now, $date_checked );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_website_item = null;
    }
}
