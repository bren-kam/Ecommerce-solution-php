<?php

require_once 'test/base-database-test.php';

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
     * Test adding all the checklist items to a checklist
     */
    public function testAddAllToChecklist() {
        // Declare variables
        $checklist_id = -5;

        // Should insert over 30 items
        $this->checklist_website_item->add_all_to_checklist( $checklist_id );

        // Get items
        $checklist_website_items = $this->phactory->get_results( "SELECT FROM `checklist_website_items` WHERE `checklist_id` = $checklist_id" );

        // Make sure that there are many of them
        $this->assertGreaterThan( count( $checklist_website_items ), 20 );

        // Delete
        $this->phactory->delete( 'checklist_website_items', array( 'checklist_id' => $checklist_id ), 'i' );
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
        $this->checklist_website_item->save();

        // Now check it!
        $date_checked = $this->phactory->get_var( 'SELECT `date_checked` FROM `checklist_website_items` WHERE `checklist_website_item_id` = ' . (int) $this->checklist_website_item->id );

        $this->assertEquals( $now, $date_checked );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_website_item = null;
    }
}
