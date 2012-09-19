<?php

require_once 'base-database-test.php';

class ChecklistWebsiteItemNoteTest extends BaseDatabaseTest {
    /**
     * @var ChecklistWebsiteItemNote
     */
    private $checklist_website_item_note;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_website_item_note = new ChecklistWebsiteItemNote();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $checklist_website_item_note_id = 16;

        $this->checklist_website_item_note->get( $checklist_website_item_note_id );

        $this->assertEquals( $this->checklist_website_item_note->id, 16 );
    }

    /**
     * Tests getting notes by a website item
     */
    public function testGetByChecklist() {
        // Declare variables
        $checklist_website_item_id = 123;

        $checklist_website_item_notes = $this->checklist_website_item_note->get_by_checklist_website_item( $checklist_website_item_id );

        $this->assertTrue( $checklist_website_item_notes[0] instanceof ChecklistWebsiteItemNote );
        $this->assertEquals( count( $checklist_website_item_notes ), 2 );
    }

    /**
     * Test creating
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->checklist_website_item_note->checklist_website_item_id = 0;
        $this->checklist_website_item_note->user_id = 514;
        $this->checklist_website_item_note->note = 'with a love that was more than love';
        $this->checklist_website_item_note->create();

        $this->assertTrue( !is_null( $this->checklist_website_item_note->id ) );

        // Make sure it's in the database
        $this->checklist_website_item_note->get( $this->checklist_website_item_note->id );

        $this->assertEquals( 'with a love that was more than love', $this->checklist_website_item_note->note );

        // Delete
        $this->db->delete( 'checklist_website_item_notes', array( 'checklist_website_item_note_id' => $this->checklist_website_item_note->id ), 'i' );
    }

    /**
     * Test Deleting an attribute
     *
     * @depends testCreate
     * @depends testGet
     */
    public function testDelete() {
        // Create
        $this->checklist_website_item_note->checklist_website_item_id = 0;
        $this->checklist_website_item_note->user_id = 514;
        $this->checklist_website_item_note->note = 'two paths diverged';
        $this->checklist_website_item_note->create();

        $checklist_website_item_note_id = $this->db->get_insert_id();

        // Get it
        $this->checklist_website_item_note->get( $checklist_website_item_note_id );

        // Delete
        $this->checklist_website_item_note->delete();

        // Make sure it doesn't exist
        $note = $this->db->get_var( "SELECT `note` FROM `checklist_website_item_notes` WHERE `checklist_website_item_note_id` = $checklist_website_item_note_id" );

        $this->assertFalse( $note );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_website_item_note = null;
    }
}
