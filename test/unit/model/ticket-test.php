<?php

require_once 'test/base-database-test.php';

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
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Test Getting a ticket
//     */
//    public function testGet() {
//        // Declare variable
//        $ticket_id = 1;
//
//        $this->ticket->get( $ticket_id );
//
//        $this->assertEquals( $this->ticket->message, 'message' );
//    }
//
//    /**
//     * Test Create
//     */
//    public function testCreate() {
//        $this->ticket->status = -5;
//        $this->ticket->create();
//
//        // Make sure it's in the database
//        $status = $this->phactory->get_var( 'SELECT `status` FROM `tickets` WHERE `ticket_id` = ' . (int) $this->ticket->id );
//
//        $this->assertEquals( $this->ticket->status, $status );
//
//        // Delete the attribute
//        $this->phactory->delete( 'tickets', array( 'ticket_id' => $this->ticket->id ), 'i' );
//    }
//
//    /**
//     * Test updating a ticket
//     *
//     * @depends testGet
//     */
//    public function testUpdate() {
//        // Declare variable
//        $ticket_id = 1;
//
//        // Update ticket to something wrong
//        $this->phactory->update( 'tickets', array( 'priority' => 2 ), array( 'ticket_id' => $ticket_id ), 'i', 'i' );
//
//        $this->ticket->get( $ticket_id );
//
//        // Update test
//        $this->ticket->priority = 1;
//        $this->ticket->save();
//
//        // Get priority
//        $priority = $this->phactory->get_var( "SELECT `priority` FROM `tickets` WHERE `ticket_id` = $ticket_id" );
//
//        $this->assertEquals( '1', $priority );
//    }
//
//    /**
//     * Test listing all companies
//     */
//    public function testListAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
//        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );
//
//        $tickets = $this->ticket->list_all( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( $tickets[0] instanceof Ticket );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $tickets );
//    }
//
//    /**
//     * Test counting the companies
//     */
//    public function testCountAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 1;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
//        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );
//
//        $count = $this->ticket->count_all( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 1, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }
//
//    /**
//     * Test deleting all uncreated tickets
//     *
//     * @depends testCreate
//     */
//    public function testDeleteUncreatedTickets() {
//        // An uncreated ticket has status of -1
//        $this->ticket->status = -1;
//        $this->ticket->create();
//
//        // Set the update time to something in the past
//        $this->phactory->update( 'tickets', array( 'date_created' => '2012-10-09 00:00:00' ), array( 'ticket_id' => $this->ticket->id ), 's', 'i' );
//
//        // Create a fake ticket upload
//        $this->phactory->insert( 'ticket_uploads', array( 'ticket_id' => $this->ticket->id, 'key' => 'url/path/file.jpg', 'date_created' => dt::now() ), 'iss' );
//
//        // Now -- delete it, it's uncreated, everything should be gone
//        $this->ticket->deleted_uncreated_tickets();
//
//        // Makes ure they are deleted
//        $tickets = $this->phactory->get_results( 'SELECT t.`ticket_id`, tu.`ticket_upload_id` FROM `tickets` AS t JOIN `ticket_uploads` AS tu ON ( tu.`ticket_id` = t.`ticket_id` ) WHERE t.`status` = -1 AND t.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )' );
//
//        $this->assertTrue( empty( $tickets ) );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->ticket = null;
    }
}
