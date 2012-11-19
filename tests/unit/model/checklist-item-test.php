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
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $checklist_item_id = 1;

        $this->checklist_item->get( $checklist_item_id );

        $this->assertEquals( $this->checklist_item->name, '1st Contact' );
    }

    /**
     * Test Get All
     */
    public function testGetAll() {
        $checklist_items = $this->checklist_item->get_all();

        $this->assertTrue( current( $checklist_items ) instanceof ChecklistItem );
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
     * Test creating
     *
     * @depends testGet
     */
    public function testCreate() {
        // Create
        $this->checklist_item->status = 0;
        $this->checklist_item->checklist_section_id = -3;
        $this->checklist_item->create();

        $this->assertTrue( !is_null( $this->checklist_item->id ) );

        // Make sure it's in the database
        $this->checklist_item->get( $this->checklist_item->id );

        $this->assertEquals( -3, $this->checklist_item->checklist_section_id );

        // Delete
        $this->db->delete( 'checklist_items', array( 'checklist_item_id' => $this->checklist_item->id ), 'i' );
    }

    /**
     * Test updating
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $this->checklist_item->status = 0;
        $this->checklist_item->checklist_section_id = -3;
        $this->checklist_item->create();

        // Update test
        $this->checklist_item->name = 'Bloom';
        $this->checklist_item->assigned_to = 'Morning Glory';
        $this->checklist_item->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->checklist_item->id ) );

        // Now check it!
        $this->checklist_item->get( $this->checklist_item->id );

        $this->assertEquals( 'Morning Glory', $this->checklist_item->assigned_to );

        // Delete
        $this->db->delete( 'checklist_items', array( 'checklist_item_id' => $this->checklist_item->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_item = null;
    }
}
