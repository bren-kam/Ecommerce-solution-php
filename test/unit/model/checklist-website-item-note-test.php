<?php

require_once 'test/base-database-test.php';

class ChecklistWebsiteItemNoteTest extends BaseDatabaseTest {
    const CHECKLIST_WEBSITE_ITEM_ID = 3;
    const NOTE = 'Pistachios';

    /**
     * @var ChecklistWebsiteItemNote
     */
    private $checklist_website_item_note;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_website_item_note = new ChecklistWebsiteItemNote();

        // Define
        $this->phactory->define( 'checklist_website_item_notes', array( 'checklist_website_item_id' => self::CHECKLIST_WEBSITE_ITEM_ID, 'note' => self::NOTE ) );
        $this->phactory->define('users');
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_checklist_website_item_note = $this->phactory->create('checklist_website_item_notes');

        // Get
        $this->checklist_website_item_note->get( $ph_checklist_website_item_note->checklist_website_item_note_id );

        // Assert
        $this->assertEquals( $ph_checklist_website_item_note->checklist_website_item_note_id, $this->checklist_website_item_note->checklist_website_item_note_id );
    }

    /**
     * Tests getting notes by a website item
     */
    public function testGetByChecklist() {
        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create( 'checklist_website_item_notes', array( 'user_id' => $ph_user->user_id ) );

        // Get
        $checklist_website_item_notes = $this->checklist_website_item_note->get_by_checklist_website_item( self::CHECKLIST_WEBSITE_ITEM_ID );
        $checklist_website_item_note = current( $checklist_website_item_notes );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ChecklistWebsiteItemNote', $checklist_website_item_notes );
        $this->assertEquals( self::NOTE, $checklist_website_item_note->note );
    }

    /**
     * Test creating
     */
    public function testCreate() {
        // Create
        $this->checklist_website_item_note->note = self::NOTE;
        $this->checklist_website_item_note->create();

        // Assert
        $this->assertNotNull( $this->checklist_website_item_note->id );

        // Make sure it's in the database
        $ph_checklist_website_item_note = $this->phactory->get( 'checklist_website_item_notes', array( 'checklist_website_item_note_id' => $this->checklist_website_item_note->id ) );

        // Assert
        $this->assertEquals( self::NOTE, $ph_checklist_website_item_note->note );
    }

    /**
     * Test Deleting
     */
    public function testDelete() {
        // Create
        $ph_checklist_website_item_note = $this->phactory->create('checklist_website_item_notes');

        // Delete
        $this->checklist_website_item_note->id = $ph_checklist_website_item_note->checklist_website_item_note_id;
        $this->checklist_website_item_note->remove();

        // Make sure it's in the database
        $ph_checklist_website_item_note = $this->phactory->get( 'checklist_website_item_notes', array( 'checklist_website_item_note_id' => $ph_checklist_website_item_note->checklist_website_item_note_id ) );

        // Assert
        $this->assertNull( $ph_checklist_website_item_note );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_website_item_note = null;
    }
}
