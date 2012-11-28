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
        $this->db->delete( 'ticket_comments', array( 'ticket_comment_id' => $this->ticket_comment->id ), 'i' );
    }

    /**
     * Test Adding industries
     *
     * @depends testGet
     */
    public function testAddUploadLinks() {
        // Declare variables
        $ticket_comment_id = 1;
        $upload_links = array( 1, 2, 3, 5 );

        // Get ticket comment
        $this->ticket_comment->get( $ticket_comment_id );

        // Add the links
        $this->ticket_comment->add_upload_links( $upload_links );

        // Get count
        $link_count = $this->db->get_var( 'SELECT COUNT(`ticket_upload_id`) FROM `ticket_comment_upload_links` WHERE `ticket_comment_id` = ' . (int) $ticket_comment_id );

        $this->assertEquals( 4, $link_count );

        // Delete links
        $this->db->query( 'DELETE FROM `ticket_comment_upload_links` WHERE `ticket_comment_id` = ' . (int) $ticket_comment_id );
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
        $fetched_ticket_comment_id = $this->db->get_var( "SELECT `ticket_comment_id` FROM `ticket_comments` WHERE `ticket_comment_id` = $ticket_comment_id" );

        $this->assertFalse( $fetched_ticket_comment_id );
    }

    /**
     * Test deleting upload links
     *
     * @depends testGet
     * @depends testAddUploadLinks
     */
    public function testDeleteUploadLinks() {
        // Declare variables
        $ticket_comment_id = 1;
        $upload_links = array( 1, 2, 3, 5 );

        // Get ticket comment
        $this->ticket_comment->get( $ticket_comment_id );

        // Add the links
        $this->ticket_comment->add_upload_links( $upload_links );

        // let's delete them
        $this->ticket_comment->delete_upload_links();

        // Check count
        $link_count = $this->db->get_var( 'SELECT COUNT(`ticket_upload_id`) FROM `ticket_comment_upload_links` WHERE `ticket_comment_id` = ' . (int) $ticket_comment_id );

        $this->assertEquals( 0, $link_count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket_comment = null;
    }
}
