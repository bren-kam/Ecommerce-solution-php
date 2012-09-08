<?php

require_once 'base-database-test.php';

class TicketCommentTest extends BaseDatabaseTest {
    /**
     * @var TicketComment
     */
    private $ticket_comment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->ticket_comment = new TicketComment();
    }

    /**
     * Test Getting all the ticket comments
     */
    public function testGetAll() {
        // Declare variables
        $ticket_id = 1;

        // Get comments
        $comments = $this->ticket_comment->get_by_ticket( $ticket_id );

        $this->assertTrue( $comments[0] instanceof TicketComment );
    }
    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket_comment = null;
    }
}
