<?php

require_once 'test/base-database-test.php';

class TicketCommentTest extends BaseDatabaseTest {
    const TICKET_ID = 3;
    const COMMENT = 'This is done';

    /**
     * @var TicketComment
     */
    private $ticket_comment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->ticket_comment = new TicketComment();

        // Define
        $this->phactory->define( 'ticket_comments', array( 'ticket_id' => self::TICKET_ID, 'comment' => self::COMMENT ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting a ticket comment
     */
    public function testGet() {
        // Create
        $ph_ticket_comment = $this->phactory->create('ticket_comments');

        // Get
        $this->ticket_comment->get( $ph_ticket_comment->ticket_comment_id );

        // Assert
        $this->assertEquals( self::COMMENT, $this->ticket_comment->comment );
    }

    /**
     * Test Getting all the ticket comments
     */
    public function testGetByTicket() {
        // Create
        $this->phactory->create('ticket_comments');

        // Get
        $ticket_comments = $this->ticket_comment->get_by_ticket( self::TICKET_ID );
        $ticket_comment = current( $ticket_comments );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'TicketComment', $ticket_comments );
        $this->assertEquals( self::COMMENT, $ticket_comment->comment );
    }

    /**
     * Test creating a ticket comment
     */
    public function testCreate() {
        // Create
        $this->ticket_comment->ticket_id = self::TICKET_ID;
        $this->ticket_comment->comment = self::COMMENT;
        $this->ticket_comment->create();

        // Assert
        $this->assertNotNull( $this->ticket_comment->id );

        // Get
        $ph_ticket_comment = $this->phactory->get( 'ticket_comments', array( 'ticket_comment_id' => $this->ticket_comment->id ) );

        // Assert
        $this->assertEquals( self::COMMENT, $ph_ticket_comment->comment );
    }

    /**
     * Test Deleting
     */
    public function testDelete() {
        // Create
        $ph_ticket_comment = $this->phactory->create('ticket_comments');

        // Delete
        $this->ticket_comment->id = $ph_ticket_comment->ticket_comment_id;
        $this->ticket_comment->remove();

        // Get
        $ph_ticket_comment = $this->phactory->get( 'ticket_comments', array( 'ticket_comment_id' => $ph_ticket_comment->ticket_comment_id ) );

        // Assert
        $this->assertNull( $ph_ticket_comment );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->ticket_comment = null;
    }
}
