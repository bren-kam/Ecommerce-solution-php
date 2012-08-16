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
        $uploads = $this->ticket_upload->get_for_ticket(33);

        $this->assertEquals( $uploads[0], '19/160/33/gsr-home.jpg' );
    }

    /**
     * Test Getting all the uploads for a ticket
     */
    public function testGetForComments() {
        $uploads = $this->ticket_upload->get_for_comments(2);

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
