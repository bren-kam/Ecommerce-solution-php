<?php

require_once 'base-database-test.php';

class TicketUploadTest extends BaseDatabaseTest {
    /**
     * @var TicketUpload
     */
    private $ticket_upload;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->ticket_upload = new TicketUpload();
    }

    /**
     * Test Getting a ticket comment
     */
    public function testGet() {
        // Declare variables
        $ticket_upload_id = 5;

        $this->ticket_upload->get( $ticket_upload_id );

        $this->assertEquals( $this->ticket_upload->key, '1/122/test.xls' );
    }

    /**
     * Test Getting all the uploads for a ticket message
     */
    public function testGetByTicket() {
        // Declare variables
        $ticket_id = 33;

        // Get uploads
        $uploads = $this->ticket_upload->get_by_ticket( $ticket_id );

        $this->assertEquals( $uploads[0], '19/160/33/gsr-home.jpg' );
    }

    /**
     * Test Getting all the uploads for a ticket's comments
     */
    public function testGetByComments() {
        // Declare variables
        $ticket_id = 2;

        // Get uploads
        $uploads = $this->ticket_upload->get_by_comments( $ticket_id );

        $this->assertTrue( $uploads[0] instanceof TicketUpload );
        $this->assertEquals( $uploads[0]->key, '/tickets/uploads/test.jpg' );
    }

    /**
     * Test Getting all the uploads for a ticket comment
     */
    public function testGetByComment() {
        // Declare variables
        $ticket_comment_id = 16;

        // Get uploads
        $uploads = $this->ticket_upload->get_by_comment( $ticket_comment_id );

        $this->assertTrue( $uploads[0] instanceof TicketUpload );
        $this->assertEquals( $uploads[0]->key, '/tickets/uploads/test.jpg' );
    }

    /**
     * Test creating a ticket upload
     *
     * @depends testGet
     */
    public function testCreate() {
        // Declare variables
        $key = 'url/path/file.jpg';

        // Create ticket upload
        $this->ticket_upload->key = $key;
        $this->ticket_upload->create();

        $this->assertTrue( !is_null( $this->ticket_upload->id ) );

        // Make sure it's in the database
        $this->ticket_upload->get( $this->ticket_upload->id );

        $this->assertEquals( $key, $this->ticket_upload->key );

        // Delete the upload
        $this->db->delete( 'ticket_uploads', array( 'ticket_upload_id' => $this->ticket_upload->id ), 'i' );
    }

    /**
     * Test Deleting
     *
     * @depends testCreate
     */
    public function testDelete() {
        // Create ticket upload
        $this->ticket_upload->key = 'url/path/file.jpg';
        $this->ticket_upload->create();

        $ticket_upload_id = (int) $this->ticket_upload->id;

        // Delete ticket upload
        $this->ticket_upload->delete();

        // Check
        $fetched_ticket_upload_id = $this->db->get_var( "SELECT `ticket_upload_id` FROM `ticket_uploads` WHERE `ticket_upload_id` = $ticket_upload_id" );

        $this->assertFalse( $fetched_ticket_upload_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket_upload = null;
    }
}
