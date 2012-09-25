<?php

require_once 'base-database-test.php';

class TicketTest extends BaseDatabaseTest {
    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->ticket = new Ticket();
    }

    /**
     * Test Getting a ticket
     */
    public function testGet() {
        // Declare variable
        $ticket_id = 1;

        $this->ticket->get( $ticket_id );

        $this->assertEquals( $this->ticket->message, 'message' );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        $this->ticket->status = -5;
        $this->ticket->create();

        $this->assertTrue( !is_null( $this->ticket->id ) );

        // Make sure it's in the database
        $status = $this->db->get_var( 'SELECT `status` FROM `tickets` WHERE `ticket_id` = ' . (int) $this->ticket->id );

        $this->assertEquals( $this->ticket->status, $status );

        // Delete the attribute
        $this->db->delete( 'tickets', array( 'ticket_id' => $this->ticket->id ), 'i' );
    }

    /**
     * Test Adding Ticket Links
     *
     * @depends testCreate
     */
    public function testAddLinks() {
        // Declare variables
        $ticket_links = array( '-10', '-5' );

        // Delete any previous relations
        $this->db->delete( 'ticket_links', array( 'ticket_id' => -3 ), 'i' );

        // Create ticket
        $this->status = 0;
        $this->ticket->create();

        // Add them
        $this->ticket->add_links( $ticket_links );

        // Now check it
        $fetched_links = $this->db->get_col( "SELECT `ticket_upload_id` FROM `ticket_links` WHERE `ticket_id` = " . (int) $this->ticket->id );

        $this->assertEquals( $ticket_links, $fetched_links );

        // Delete links and ticket
        $this->db->delete( 'ticket_links', array( 'ticket_id' => $this->ticket->id ), 'i' );
        $this->db->delete( 'tickets', array( 'ticket_id' => $this->ticket->id ), 'i' );
    }

    /**
     * Test updating a ticket
     *
     * @depends testGet
     */
    public function testUpdate() {
        // Declare variable
        $ticket_id = 1;

        // Update ticket to something wrong
        $this->db->update( 'tickets', array( 'priority' => 2 ), array( 'ticket_id' => $ticket_id ), 'i', 'i' );

        $this->ticket->get( $ticket_id );

        // Update test
        $this->ticket->priority = 1;
        $this->ticket->update();

        // Get priority
        $priority = $this->db->get_var( "SELECT `priority` FROM `tickets` WHERE `ticket_id` = $ticket_id" );

        $this->assertEquals( '1', $priority );
    }

    /**
     * Test listing all companies
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );

        $tickets = $this->ticket->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( $tickets[0] instanceof Ticket );

        // Get rid of everything
        unset( $user, $_GET, $dt, $tickets );
    }

    /**
     * Test counting the companies
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );

        $count = $this->ticket->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket = null;
    }
}
