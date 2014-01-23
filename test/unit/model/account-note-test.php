<?php

require_once 'test/base-database-test.php';

class AccountNoteTest extends BaseDatabaseTest {
    const MESSAGE = 'This account is...';
    const USER_ID = 1;

    /**
     * @var AccountNote
     */
    private $account_note;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_note = new AccountNote();

        // Define
        $this->phactory->define( 'website_notes', array( 'website_id' => self::WEBSITE_ID, 'message' => self::MESSAGE ) );
        $this->phactory->recall();
    }

    /**
     * Test creating the note
     */
    public function testCreate() {
        // Create
        $this->account_note->user_id = self::USER_ID;
        $this->account_note->website_id = self::WEBSITE_ID;
        $this->account_note->message = self::MESSAGE;
        $this->account_note->create();

        $this->assertTrue( !is_null( $this->account_note->id ) );

        // Get
        $ph_account_file = $this->phactory->get( 'website_notes', array( 'website_note_id' => $this->account_note->id ) );

        $this->assertEquals( self::MESSAGE, $ph_account_file->message );
    }

    /**
     * Test getting a note
     */
    public function testGet() {
        // Insert
        $ph_website_note = $this->phactory->create( 'website_notes' );

        // Get
        $this->account_note->get( $ph_website_note->website_note_id );

        $this->assertEquals( self::MESSAGE, $this->account_note->message );
    }

    /**
     * Test deleting a note
     *
     * @depends testGet
     */
    public function testDelete() {
        // Insert
        $ph_website_note = $this->phactory->create( 'website_notes' );

        // Delete it!
        $this->account_note->id = $ph_website_note->website_note_id;
        $this->account_note->delete();

        // Shouldn't exist
        $ph_website_note = $this->phactory->get( 'website_notes', array( 'website_note_id' => $ph_website_note->website_note_id ) );

        $this->assertNull( $ph_website_note );
    }

    /**
     * Test get all notes for an account
     */
    public function testGetAll() {
         // Insert
        $this->phactory->create( 'website_notes' );

        // Get the notes
        $notes = $this->account_note->get_all( self::WEBSITE_ID );
        $note = current( $notes );

        $this->assertContainsOnlyInstancesOf( 'AccountNote', $notes );
        $this->assertEquals( self::MESSAGE, $note->message );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_note = null;
    }
}
