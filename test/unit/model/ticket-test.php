<?php

require_once 'test/base-database-test.php';

class TicketTest extends BaseDatabaseTest {
    const SUMMARY = 'Problem with Lift Wedges';
    const DATE_CREATED = '2014-01-01 00:00:00';

    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->ticket = new Ticket();

        // Define
        $this->phactory->define( 'tickets', array( 'summary' => self::SUMMARY, 'priority' => Ticket::PRIORITY_NORMAL, 'status' => Ticket::STATUS_OPEN, 'date_created' => self::DATE_CREATED ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting a ticket
     */
    public function testGet() {
        // Create
        $ph_ticket = $this->phactory->create('tickets');

        // Get
        $this->ticket->get( $ph_ticket->ticket_id );

        // Get
        $this->assertEquals( self::SUMMARY, $this->ticket->summary );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->ticket->summary = self::SUMMARY;
        $this->ticket->create();

        // Assert
        $this->assertNotNull( $this->ticket->id );

        // Get
        $ph_ticket = $this->phactory->get( 'tickets', array( 'ticket_id' => $this->ticket->id ) );

        // Assert
        $this->assertEquals( self::SUMMARY, $ph_ticket->summary );
    }

    /**
     * Test updating a ticket
     *
     * @depends testGet
     */
    public function testUpdate() {
        // Create
        $ph_ticket = $this->phactory->create('tickets');

        // Update test
        $this->ticket->id = $ph_ticket->ticket_id;
        $this->ticket->summary = 'Mumkins Go Jumping At Night';
        $this->ticket->save();

        // Get
        $ph_ticket = $this->phactory->get( 'tickets', array( 'ticket_id' => $this->ticket->id ) );

        // Assert
        $this->assertEquals( $this->ticket->summary, $ph_ticket->summary );
    }

    /**
     * Test listing all companies
     */
    public function testListAll() {
        // Get Mock
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('tickets');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );

        // Get
        $tickets = $this->ticket->list_all( $dt->get_variables() );
        $ticket = current( $tickets );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'Ticket', $tickets );
        $this->assertEquals( self::SUMMARY, $ticket->summary );

        // Get rid of everything
        unset( $user, $_GET, $dt, $tickets );
    }

    /**
     * Test counting the companies
     */
    public function testCountAll() {
        // Get Mock
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('tickets');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );

        // Get
        $count = $this->ticket->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Test deleting all uncreated tickets
     */
    public function testDeleteUncreatedTickets() {
        // Create
        $this->phactory->create( 'tickets', array( 'status' => Ticket::STATUS_UNCREATED ) );

        // Delete
        $this->ticket->deleted_uncreated_tickets();

        // Get
        $ph_ticket = $this->phactory->get( 'tickets', array( 'status' => Ticket::STATUS_UNCREATED ) );

        // Assert
        $this->assertNull( $ph_ticket );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket = null;
    }
}
