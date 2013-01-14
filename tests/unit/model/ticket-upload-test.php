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
        $ticket_id = -99;
        $ticket_comment_id = -459;
        $ticket_upload_id = -37;
        $key = 'hey-hey';

        // Create a comment
        $this->db->insert( 'ticket_comments', compact( 'ticket_comment_id', 'ticket_id' ), 'ii' );
        $this->db->insert( 'ticket_comment_upload_links', compact( 'ticket_comment_id', 'ticket_upload_id' ), 'ii' );
        $this->db->insert( 'ticket_uploads', compact( 'ticket_upload_id', 'key' ), 'is' );

        // Get uploads
        $uploads = $this->ticket_upload->get_by_comments( $ticket_id );

        $this->assertTrue( current( $uploads ) instanceof TicketUpload );
    }

    /**
     * Test Getting all the uploads for a ticket comment
     */
    public function testGetByComment() {
        // Declare variables
        $ticket_comment_id = -459;
        $ticket_upload_id = -37;
        $key = 'hey-hey';

        // Create a comment
        $this->db->insert( 'ticket_comment_upload_links', compact( 'ticket_comment_id', 'ticket_upload_id' ), 'ii' );
        $this->db->insert( 'ticket_uploads', compact( 'ticket_upload_id', 'key' ), 'is' );

        // Get uploads
        $uploads = $this->ticket_upload->get_by_comment( $ticket_comment_id );

        $this->assertTrue( current( $uploads ) instanceof TicketUpload );
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
     * Test Getting keys by uncreated tickets (so that we can remove uploads)
     *
     * @depends testCreate
     */
    public function testGetKeysByUncreatedTickets() {
        // Create ticket uploads
        $this->ticket_upload->key = 'url/path/file.jpg';
        $this->ticket_upload->create();
        $ticket_upload_id = $this->ticket_upload->id;

        $this->ticket_upload->key = 'url/path/file2.jpg';
        $this->ticket_upload->create();
        $ticket_upload_id2 = $this->ticket_upload->id;

        // Create ticket
        $this->db->insert( 'tickets', array( 'status' => -1, 'date_created' => '2012-10-09 00:00:00' ), 'is' );
        $ticket_id = $this->db->get_insert_id();

        // Create a link
        $this->db->insert( 'ticket_links', array( 'ticket_id' => $ticket_id, 'ticket_upload_id' => $ticket_upload_id ), 'ii' );
        $this->db->insert( 'ticket_links', array( 'ticket_id' => $ticket_id, 'ticket_upload_id' => $ticket_upload_id2 ), 'ii' );

        // Now, let's get the keys
        $keys = $this->ticket_upload->get_keys_by_uncreated_tickets();

        $this->assertEquals( array( 'url/path/file.jpg', 'url/path/file2.jpg' ), $keys );

        // Now delete everything
        $this->db->query( "DELETE tu.*, tl.*, t.* FROM `ticket_uploads` AS tu LEFT JOIN `ticket_links` AS tl ON ( tl.`ticket_upload_id` = tu.`ticket_upload_id` ) LEFT JOIN `tickets` AS t ON ( t.`ticket_id` = tl.`ticket_id` ) WHERE t.`ticket_id` = $ticket_id AND t.`status` = -1");
    }

    /**
     * Test Deleting
     *
     * @depends testCreate
     */
    public function testDeleteUpload() {
        // Create ticket upload
        $this->ticket_upload->key = 'url/path/file.jpg';
        $this->ticket_upload->create();

        $ticket_upload_id = (int) $this->ticket_upload->id;

        // Delete ticket upload
        $this->ticket_upload->delete_upload();

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
