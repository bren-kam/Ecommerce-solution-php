<?php

require_once 'base-database-test.php';

class AccountNoteTest extends BaseDatabaseTest {
    /**
     * @var AccountNote
     */
    private $account_note;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_note = new AccountNote();
    }

    /**
     * Test creating the note
     */
    public function testCreate() {
        $this->account_note->user_id = 1;
        $this->account_note->website_id = 96;
        $this->account_note->message = 'test';
        $this->account_note->create();

        $this->assertTrue( !is_null( $this->account_note->id ) );

        // Get the message
        $message = $this->db->get_var( 'SELECT `message` FROM `website_notes` WHERE `website_note_id` = ' . (int) $this->account_note->id );

        $this->assertEquals( $message, 'test' );

        // Delete the note
        $this->db->delete( 'website_notes', array( 'website_note_id' => $this->account_note->id ), 'i' );
    }

    /**
     * Test getting a note
     */
    public function testGet() {
        // Insert a note
        $this->db->insert( 'website_notes', array( 'website_id' => 96, 'user_id' => 1, 'message' => 'test' ), 'iis' );
        $website_note_id = $this->db->get_insert_id();

        // Get it
        $this->account_note->get($website_note_id);

        $this->assertEquals( $this->account_note->message, 'test' );

        // Delete the note
        $this->db->delete( 'website_notes', array( 'website_note_id' => $website_note_id ), 'i' );
    }

    /**
     * Test deleting a note
     *
     * @depends testGet
     */
    public function testDelete() {
        // Insert a note
        $this->db->insert( 'website_notes', array( 'website_id' => 96, 'user_id' => 1, 'message' => 'test' ), 'iis' );
        $website_note_id = $this->db->get_insert_id();

        // Make sure it exists
        $this->account_note->get( $website_note_id );

        // Delete it!
        $this->account_note->delete();

        // Shouldn't exist
        $message = $this->db->get_var( 'SELECT `message` FROM `website_notes` WHERE `website_note_id` = ' . (int) $website_note_id );

        $this->assertFalse( $message );
    }

    /**
     * Test get all notes for an account
     */
    public function testGetAll() {
        // Declare variables
        $account_id = 160; // Connells

        // Get the notes
        $notes = $this->account_note->get_all( $account_id );

        $this->assertTrue( current( $notes ) instanceof AccountNote );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_note = null;
    }
}
