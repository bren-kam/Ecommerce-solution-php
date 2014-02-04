<?php

require_once 'test/base-database-test.php';

class TicketUploadTest extends BaseDatabaseTest {
    const TICKET_ID = 3;
    const TICKET_COMMENT_ID = 7;
    const KEY = '123/321/feeling.rig';

    // Tickets
    const DATE_CREATED = '2014-01-01 00:00:00';

    /**
     * @var TicketUpload
     */
    private $ticket_upload;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->ticket_upload = new TicketUpload();

        // Define
        $this->phactory->define( 'ticket_uploads', array( 'ticket_id' => self::TICKET_ID, 'ticket_comment_id' => self::TICKET_COMMENT_ID, 'key' => self::KEY ) );
        $this->phactory->define( 'ticket_comments', array( 'ticket_id' => self::TICKET_ID ) );
        $this->phactory->define( 'tickets', array( 'status' => Ticket::STATUS_UNCREATED, 'date_created' => self::DATE_CREATED ) );
        $this->phactory->recall();
    }


    /**
     * Test Getting a ticket comment
     */
    public function testGet() {
        // Create
        $ph_ticket_upload = $this->phactory->create('ticket_uploads');

        // Get
        $this->ticket_upload->get( $ph_ticket_upload->ticket_upload_id );

        // Assert
        $this->assertEquals( self::KEY, $this->ticket_upload->key );
    }

    /**
     * Test Getting all the uploads for a ticket message
     */
    public function testGetByTicket() {
        // Create
        $this->phactory->create('ticket_uploads');

        // Get
        $ticket_uploads = $this->ticket_upload->get_by_ticket( self::TICKET_ID );
        $expected_ticket_uploads = array( self::KEY );

        // Assert
        $this->assertEquals( $expected_ticket_uploads, $ticket_uploads );
    }

    /**
     * Test Getting all the uploads for a ticket's comments
     */
    public function testGetByComments() {
        // Create
        $ph_ticket_comment = $this->phactory->create('ticket_comments');
        $this->phactory->create( 'ticket_uploads', array( 'ticket_comment_id' => $ph_ticket_comment->ticket_comment_id ) );

        // Get
        $ticket_uploads = $this->ticket_upload->get_by_comments( self::TICKET_ID );
        $ticket_upload = current( $ticket_uploads );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'TicketUpload', $ticket_uploads );
        $this->assertEquals( self::KEY, $ticket_upload->key );
    }

    /**
     * Test Getting all the uploads for a ticket comment
     */
    public function testGetByComment() {
        // Create
        $this->phactory->create('ticket_uploads');

        // Get uploads
        $ticket_uploads = $this->ticket_upload->get_by_comment( self::TICKET_COMMENT_ID );
        $ticket_upload = current( $ticket_uploads );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'TicketUpload', $ticket_uploads );
        $this->assertEquals( self::KEY, $ticket_upload->key );
    }

    /**
     * Test creating a ticket upload
     */
    public function testCreate() {
        // Create
        $this->ticket_upload->key = self::KEY;
        $this->ticket_upload->create();

        // Assert
        $this->assertNotNull( $this->ticket_upload->id );

        // Get
        $ph_ticket_upload = $this->phactory->get( 'ticket_uploads', array( 'ticket_upload_id' => $this->ticket_upload->id ) );

        // Assert
        $this->assertEquals( self::KEY, $ph_ticket_upload->key );
    }

    /**
     * Test Getting keys by uncreated tickets (so that we can remove uploads)
     */
    public function testGetKeysByUncreatedTickets() {
        // Create
        $ph_ticket = $this->phactory->create('tickets');
        $this->phactory->create( 'ticket_uploads', array( 'ticket_id' => $ph_ticket->ticket_id ) );

        // Get
        $keys = $this->ticket_upload->get_keys_by_uncreated_tickets();
        $expected_keys = array( self::KEY );

        // Assert
        $this->assertEquals( $expected_keys, $keys );
    }

    /**
     * Test Adding Ticket Links
     */
    public function testAddRelations() {
        // Declare
        $ticket_id = 4;

        // Create
        $ph_ticket_upload = $this->phactory->create( 'ticket_uploads', array( 'ticket_id' => 0 ) );

        // Add
        $this->ticket_upload->add_relations( $ticket_id, array( $ph_ticket_upload->ticket_upload_id ) );

        // Get
        $ph_ticket_upload = $this->phactory->get( 'ticket_uploads', compact('ticket_id') );

        // Assert
        $this->assertEquals( self::KEY, $ph_ticket_upload->key );
    }

    /**
     * Test Deleting
     */
    public function testDeleteUpload() {
        // Create
        $ph_ticket_upload = $this->phactory->create('ticket_uploads');

        // Delete ticket upload
        $this->ticket_upload->id = $ph_ticket_upload->ticket_upload_id;
        $this->ticket_upload->remove();

        // Get
        $ph_ticket_upload = $this->phactory->get( 'ticket_uploads', array( 'ticket_upload_id' => $ph_ticket_upload->ticket_upload_id ) );

        // Assert
        $this->assertNull( $ph_ticket_upload );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket_upload = null;
    }
}
