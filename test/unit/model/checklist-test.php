<?php

require_once 'test/base-database-test.php';

class ChecklistTest extends BaseDatabaseTest {
    /**
     * @var Checklist
     */
    private $checklist;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist = new Checklist();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $checklist_id = 1;

        $this->checklist->get( $checklist_id );

        $this->assertEquals( $this->checklist->website_id, 57 );
    }

    /**
     * Test Create
     *
     * @depends testGet
     */
    public function testCreate() {
        // Declare variables
        $account_id = -5;

        $this->checklist->website_id = $account_id;
        $this->checklist->type = 'Website Setup';
        $this->checklist->create();

        $this->assertTrue( !is_null( $this->checklist->id ) );

        // Make sure it's in the database
        $this->checklist->get( $this->checklist->id );

        $this->assertTrue( !is_null( $this->checklist->website_id ) );

        // Delete the account
        $this->phactory->delete( 'checklists', array( 'checklist_id' => $this->checklist->id ), 'i' );
    }

    /**
     * Tests getting incomplete checklists
     */
    public function testGetIncomplete() {
        $incomplete_checklists = $this->checklist->get_incomplete();

        $this->assertTrue( is_array( $incomplete_checklists ) );
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
        $dt->order_by( 'days_left', 'w.`title`', 'u2.`contact_name`', 'c.`type`', 'c.`date_created`' );
        $dt->search( array( 'w.`title`' => false ) );

        $checklists = $this->checklist->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( $checklists[0] instanceof Checklist );

        // Get rid of everything
        unset( $user, $_GET, $dt, $checklists );
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
        $dt->order_by( 'days_left', 'w.`title`', 'u2.`contact_name`', 'c.`type`', 'c.`date_created`' );
        $dt->search( array( 'w.`title`' => false ) );

        $count = $this->checklist->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist = null;
    }
}
