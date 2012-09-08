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
     * Test Getting all the uploads for a ticket
     */
    public function testGetForTicket() {
        // Declare variables
        $ticket_id = 33;

        // Get uploads
        $uploads = $this->ticket_upload->get_by_ticket( $ticket_id );

        $this->assertEquals( $uploads[0], '19/160/33/gsr-home.jpg' );
    }

    /**
     * Test Getting all the uploads for a ticket
     */
    public function testGetForComments() {
        // Declare variables
        $ticket_id = 2;

        // Get uploads
        $uploads = $this->ticket_upload->get_by_comments( $ticket_id );

        $this->assertTrue( $uploads[0] instanceof TicketUpload );
        $this->assertEquals( $uploads[0]->key, '/tickets/uploads/test.jpg' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket_upload = null;
    }
}
