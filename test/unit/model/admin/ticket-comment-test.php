<?php

require_once 'test/base-database-test.php';

class TicketCommentTest extends BaseDatabaseTest {
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
    }

    /**
     * Test Getting a ticket comment
     */
    public function testGet() {
        // Declare variables
        $ticket_comment_id = 1;

        $this->ticket_comment->get( $ticket_comment_id );

        $this->assertEquals( $this->ticket_comment->comment, 'test' );
    }

    /**
     * Test Getting all the ticket comments
     */
    public function testGetByTicket() {
        // Declare variables
        $ticket_id = 1;

        // Get comments
        $comments = $this->ticket_comment->get_by_ticket( $ticket_id );

        $this->assertTrue( $comments[0] instanceof TicketComment );
    }

    /**
     * Test creating a ticket comment
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->ticket_comment->ticket_id = -3;
        $this->ticket_comment->user_id = 514;
        $this->ticket_comment->comment = 'Gobbledygook';
        $this->ticket_comment->private = 0;
        $this->ticket_comment->create();

        $this->assertTrue( !is_null( $this->ticket_comment->id ) );

        // Make sure it's in the database
        $this->ticket_comment->get( $this->ticket_comment->id );

        $this->assertEquals( 'Gobbledygook', $this->ticket_comment->comment );

        // Delete the comment
        $this->phactory->delete( 'ticket_comments', array( 'ticket_comment_id' => $this->ticket_comment->id ), 'i' );
    }

    /**
     * Test Deleting
     *
     * @depends testCreate
     */
    public function testDelete() {
        // Create ticket
        $this->ticket_comment->ticket_id = -3;
        $this->ticket_comment->user_id = 514;
        $this->ticket_comment->comment = 'Double, double toil and trouble';
        $this->ticket_comment->private = 0;
        $this->ticket_comment->create();

        $ticket_comment_id = (int) $this->ticket_comment->id;

        // Delete ticket comment
        $this->ticket_comment->delete();

        // Check
        $fetched_ticket_comment_id = $this->phactory->get_var( "SELECT `ticket_comment_id` FROM `ticket_comments` WHERE `ticket_comment_id` = $ticket_comment_id" );

        $this->assertFalse( $fetched_ticket_comment_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->ticket_comment = null;
    }
}
