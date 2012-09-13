<?php

require_once 'base-database-test.php';

class ChecklistItemTest extends BaseDatabaseTest {
    /**
     * @var ChecklistItem
     */
    private $checklist_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_item = new ChecklistItem();
    }

    /**
     * Tests getting incomplete checklists
     */
    public function testGetByChecklist() {
        // Declare variables
        $checklist_id = 1;

        $checklist_items = $this->checklist_item->get_by_checklist( $checklist_id );

        $this->assertTrue( $checklist_items[0] instanceof ChecklistItem );
        $this->assertEquals( count( $checklist_items ), 35 );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_item = null;
    }
}
